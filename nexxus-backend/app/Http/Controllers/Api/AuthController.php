<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LoginAttemptsExceeded;
use App\Models\User;
use http\Env\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

final class AuthController extends Controller
{
    final public function register(): JsonResponse
    {
        \Illuminate\Support\Facades\Request::validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:15|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',      // at least one letter lowercase
                'regex:/[A-Z]/',      // at least one letter uppercase
                'regex:/[0-9]/',      // at least one digit
                'regex:/[@$!%*#?&]/', // at least one special character
            ],
        ]);

        $user = User::create([
            'name' => request()->name,
            'username' => request()->username,
            'email' => request()->email,
            'password' => Hash::make(request()->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
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
            RateLimiter::hit($attemptsKey, 15); // Increment attempts for 15 minutes
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
            false,                   // Secure (true for HTTPS)
            true                    // HttpOnly (XSS protection)
        );
    }

    final public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out'
        ])->withCookie(cookie()->forget('auth_token'));
    }
}
