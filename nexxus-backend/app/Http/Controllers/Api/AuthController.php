<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LoginAttemptsExceeded;
use App\Mail\VerifyEmail;
use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

final class AuthController extends Controller
{
    public function registerPending(): JsonResponse
    {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:15|regex:/^[a-zA-Z0-9_]+$/|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ]);

        $pendingUser = PendingUser::where('email', $validated['email'])->first();

        if ($pendingUser) {
            Mail::to($validated['email'])->send(new VerifyEmail($pendingUser->verification_token));
            return response()->json([
                'message' => 'This email is already registered but not verified. We’ve resent the verification email.',
                'token' => $pendingUser->verification_token, // Debug
            ], 202);
        }

        $token = Str::random(60);
        $pendingUser = PendingUser::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'verification_token' => $token,
        ]);

        Mail::to($validated['email'])->send(new VerifyEmail($token));

        return response()->json([
            'message' => 'Please verify your email to complete registration',
            'email' => $validated['email'], // Send email for frontend
        ], 202);
    }

    public function verifyEmail(string $token): JsonResponse
    {
        $pendingUser = PendingUser::where('verification_token', $token)->first();

        if (!$pendingUser) {
            return response()->json(['message' => 'Token inválido o expirado'], 404);
        }

        $user = DB::transaction(function () use ($pendingUser) {
            $user = User::create([
                'name' => $pendingUser->name,
                'username' => $pendingUser->username,
                'email' => $pendingUser->email,
                'password' => $pendingUser->password,
                'email_verified_at' => now(),
            ]);

            $pendingUser->delete();

            return $user;
        });

        $token = $user->createToken(('web'), ['*'])->plainTextToken;

        return response()->json(['token' => $token])
            ->cookie(
                'auth_token',
                $token,
                60,                   // Expiration (1 hour)
                '/',                    // Path (root for all subdomains)
                null,                 // Domain (null for current domain)
                true,                // Secure (true for HTTPS)
                true                 // HttpOnly (XSS protection));
            );
    }

    public function checkVerification(Request $request): JsonResponse
    {
        $email = $request->query('email');
        $user = User::where('email', $email)->first();

        if ($user && !is_null($user->email_verified_at)) {
            $token = $user->createToken(('web'), ['*'])->plainTextToken;
            return response()->json(['token' => $token, 'verified' => true])
                ->cookie(
                    'auth_token',
                    $token,
                    60,                   // Expiration (1 hour)
                    '/',                    // Path (root for all subdomains)
                    null,                 // Domain (null for current domain)
                    true,                // Secure (true for HTTPS)
                    true                 // HttpOnly (XSS protection));
                );
        }

        return response()->json(['verified' => false]);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $pendingUser = PendingUser::where('email', $request->email)->first();

        if (!$pendingUser) {
            return response()->json(['message' => 'No pending registration found'], 404);
        }

        $token = $pendingUser->verification_token;
        Mail::to($pendingUser->email)->send(new \App\Mail\VerifyEmail($token));

        return response()->json(['message' => 'Verification email resent']);
    }

    final public function login(): JsonResponse
    {
        $validated = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
            'remember_me' => 'boolean',
        ]);

        // Check if the user is trying to log in too many times
        $attemptsKey = 'login_attempts_' . $validated['email'];
        if (RateLimiter::tooManyAttempts($attemptsKey, 5)) {
            $seconds = RateLimiter::availableIn($attemptsKey);
            return response()->json([
                'message' => "Too many login attempts. Please try again in $seconds seconds."
            ], 429);
        }

        // Check if the user exists and the password is correct
        $user = User::where('email', $validated['email'])->first();

        // If the user does not exist or the password is incorrect, increment the attempts counter
        if (!$user || !Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            RateLimiter::hit($attemptsKey, 30*60); // 30 minutes lockout
            $attempts = RateLimiter::attempts($attemptsKey);

            // If the user has exceeded the maximum number of attempts, send an email notification and block the account for 15 minutes
            if ($attempts >= 5 && $user) {
                $ipAddress = request()->ip();
                Mail::to($validated['email'])->send(new LoginAttemptsExceeded($user, $ipAddress));

                $seconds = RateLimiter::availableIn($attemptsKey);
                return response()->json([
                    'message' => "Too m any login attempts. Please try again in $seconds seconds."
                ], 429);
            }

            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Successful login, clear the attempts counter and generate a token
        RateLimiter::clear($attemptsKey);

        // If remember_me is true, set the expiration to 30 days, otherwise 1 hour
        $expiration = $validated['remember_me'] ? now()->addMonth() : now()->addHour();
        $token = $user->createToken($validated['device_name'], ['*'], $expiration)->plainTextToken;
        return response()->json(['token' => $token])->cookie(
            'auth_token',
            $token,
            $validated['remember_me'] ? 43200 : 60, // Expiration (30 days or 1 hour)
            '/',                    // Path (root for all subdomains)
            null,                   // Domain (null for current domain)
            true,                   // Secure (true for HTTPS)
            true                    // HttpOnly (XSS protection)
        );
    }

    final public function logout(Request $request): JsonResponse
    {
        $request -> user()->tokens()->delete();

        $response = response()->json(['message' => 'Logged out']);

        // Eliminar auth_token
        $response->withCookie(Cookie::forget('auth_token'));

        // Eliminar XSRF-TOKEN
        $response->withCookie(Cookie::forget('XSRF-TOKEN'));

        // Eliminar laravel_session (si existe)
        $response->withCookie(Cookie::forget('laravel_session'));

        return $response;

    }
}
