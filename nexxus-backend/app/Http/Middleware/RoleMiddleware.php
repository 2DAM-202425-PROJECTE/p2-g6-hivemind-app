<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect()->route('login'); // Redirigir a la página de inicio de sesión
        }

        // Verificar si el usuario tiene al menos uno de los roles permitidos
        $userRoles = auth()->user()->roles ?? [];
        if (!array_intersect($roles, $userRoles)) {
            Auth::guard('web')->logout();

            // Invalidar la sesión y regenerar
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $response = redirect()->route('forbidden');

            return $response;
        }

        return $next($request);
    }
}
