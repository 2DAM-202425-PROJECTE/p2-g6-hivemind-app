<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\AuthController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && is_null(Auth::user()->email_verified_at)) {
            // Revocar tokens para API
            if ($request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }

            // Eliminar cookies
            $response = response()->json(['message' => 'Your email address is not verified.'], 403);
            $response->withCookie(Cookie::forget('auth_token'));
            $response->withCookie(Cookie::forget('XSRF-TOKEN'));
            $response->withCookie(Cookie::forget('laravel_session'));


            return $response;
        }

        return $next($request);
    }
}
