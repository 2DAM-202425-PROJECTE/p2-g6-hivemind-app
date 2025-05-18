<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckProfileCompleted
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->is_profile_completed) {
            return redirect('/complete-profile');
        }
        return $next($request);
    }
}
