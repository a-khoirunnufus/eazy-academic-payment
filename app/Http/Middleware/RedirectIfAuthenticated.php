<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasHomepage;

class RedirectIfAuthenticated
{
    use HasHomepage;

    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect($this->getHompagePath());
            }
        }

        return $next($request);
    }
}
