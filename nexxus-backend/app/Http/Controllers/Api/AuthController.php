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
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 30; // in minutes

    public function registerPending(): JsonResponse
    {
        // Validate the request
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

        // Check if the email is already registered
        $pendingUser = PendingUser::where('email', $validated['email'])->first();

        // If the email is already registered but not verified, resend the verification email
        if ($pendingUser) {
            Mail::to($validated['email'])->send(new VerifyEmail($pendingUser->verification_token));
            return response()->json([
                'message' => 'This email is already registered but not verified. We’ve resent the verification email.',
            ], 202);
        }

        // Generate a unique verification token and create a new pending user
        $token = Str::random(60);
        PendingUser::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'verification_token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        // Send the verification email
        Mail::to($validated['email'])->send(new VerifyEmail($token));


        return response()->json([
            'message' => 'Please verify your email to complete registration',
        ], 202);
    }

    public function verifyEmail(string $token): JsonResponse
    {
        // Find the pending user by the verification token
        $pendingUser = PendingUser::where('verification_token', $token)->first();

        // If the pending user does not exist or the token has expired, return an error
        if (!$pendingUser || $pendingUser->expires_at < now()) {
            return response()->json(['message' => 'Invalid or expired token'], 404);
        }

        // After verifying the token, create a new user and delete the pending user
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

        // Send a welcome email to the new user and the temporary token
        $token = $user->createToken(('web'), ['*'])->plainTextToken;
        return response()->json(['message' => 'Logged in successfully', 'token' => $token]);
    }

    public function checkVerification(Request $request): JsonResponse
    {
        $email = $request->query('email');
        $user = User::where('email', $email)->first();

        // If the user exists and the email is verified, return a success response
        if ($user && !is_null($user->email_verified_at)) {
            $token = $user->createToken(('web'), ['*'])->plainTextToken; // Temporary token
            return response()->json(['message' => 'Logged in successfully', 'token' => $token]);
        }

        // If the user does not exist or the email is not verified, return a failure response
        return response()->json(['verified' => false]);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $pendingUser = PendingUser::where('email', $request->email)->first();

        // If the pending user does not exist, return an error
        if (!$pendingUser) {
            return response()->json(['message' => 'No pending registration found'], 404);
        }

        // Generate a new verification token and update the pending user
        $newToken = Str::random(60);
        $pendingUser->update([
            'verification_token' => $newToken,
            'expires_at' => now()->addHours(24),
        ]);

        Mail::to($pendingUser->email)->send(new \App\Mail\VerifyEmail($pendingUser->verification_token));

        return response()->json(['message' => 'Verification email resent']);
    }

    final public function login(Request $request): JsonResponse
    {
        // Validate the request
        $validated = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember_me' => 'boolean',
        ]);

        // Limit login attempts, allow 5 attempts per 30 minutes
        $attemptsKey = 'login_attempts_' . $validated['email'];
        if (RateLimiter::tooManyAttempts($attemptsKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($attemptsKey);
            return response()->json([
                'message' => "Too many login attempts. Please try again in $seconds seconds."
            ], 429);
        }

        // If user exist in pending_users table, show check-email page
        $pendingUser = PendingUser::where('email', $validated['email'])->first();
        if ($pendingUser && Hash::check($validated['password'], $pendingUser->password)) {

            // Generate a new verification token and update the pending user
            $newToken = Str::random(60);
            $pendingUser->update([
                'verification_token' => $newToken,
                'expires_at' => now()->addHours(24),
            ]);

            Mail::to($validated['email'])->send(new VerifyEmail($pendingUser->verification_token));
            return response()->json([
                'message' => 'Email not verified',
            ], 403);
        }

        $credentials = ['email' => $validated['email'], 'password' => $validated['password']];

        // Auth with Auth::attempt()
        if (!Auth::attempt($credentials, $validated['remember_me'] ?? false)) {
            RateLimiter::hit($attemptsKey, self::LOCKOUT_TIME * 60);
            $attempts = RateLimiter::attempts($attemptsKey);

            $user = User::where('email', $validated['email'])->first(); // Només per al correu
            if ($attempts >= self::MAX_ATTEMPTS && $user) {
                $ipAddress = $request->ip();
                Mail::to($validated['email'])->send(new LoginAttemptsExceeded($user, $ipAddress));
                $seconds = RateLimiter::availableIn($attemptsKey);
                return response()->json([
                    'message' => "Too many login attempts. Please try again in $seconds seconds."
                ], 429);
            }

            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Get the authenticated user
        $user = Auth::user();

        if ($user->email_verified_at === null) {
            Auth::logout();
            return response()->json(['message' => 'Email not verified'], 403);
        }

        // Clear the login attempts if successful
        RateLimiter::clear($attemptsKey);

        $token = $user->createToken('web', ['*'])->plainTextToken; // Temporary token

        // Regenerate the session to prevent session fixation attacks
        $request->session()->regenerate();
        return response()->json(['message' => 'Logged in successfully', 'token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        // Revoke the user's token, invalidate the session
        Auth::guard('web')->logout();
        $request->session()->invalidate();

        // Clear the session cookies
        return response()->json(['message' => 'Logged out'])
            ->withCookie(Cookie::forget('laravel_session'))
            ->withCookie(Cookie::forget('XSRF-TOKEN'));
    }
}
