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
                'regex:/[a-z]/',      // al menos una letra minúscula
                'regex:/[A-Z]/',      // al menos una letra mayúscula
                'regex:/[0-9]/',      // al menos un número
                'regex:/[@$!%*#?&]/', // al menos un carácter especial
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
            'device_name' => 'required'
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
            RateLimiter::hit($attemptsKey, 1 * 60); // Increment attempts for 15 minutes
            $attempts = RateLimiter::attempts($attemptsKey);

            // If the user has exceeded the maximum number of attempts, send an email notification and block the account for 15 minutes
            if ($attempts >= 5 && $user) {
                $ipAddress = request()->ip();
                Mail::to($validated['email'])->send(new \App\Mail\LoginAttemptsExceeded($user, $ipAddress));

                $seconds = RateLimiter::availableIn($attemptsKey);
                return response()->json([
                    'message' => "Too many login attempts. Please try again in $seconds seconds."
                ], 429);
            }

            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Successful login, clear the attempts counter and generate a token
        RateLimiter::clear($attemptsKey);
        $token = $user->createToken($validated['device_name'])->plainTextToken; // Generate a token for the user with the device name

        return response()->json(['token' => $token]);
    }

    final public function logout(): JsonResponse
    {
        \Illuminate\Support\Facades\Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }
}
