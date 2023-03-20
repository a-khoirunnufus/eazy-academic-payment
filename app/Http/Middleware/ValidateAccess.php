<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateAccess
{
    public function handle(Request $request, Closure $next)
    {
        $currentPath = $request->path();
        if($currentPath[0] != "/")
            $currentPath = "/".$currentPath;

        if(!auth()->canAccessPath($currentPath))
            abort(401);
            
        return $next($request);
    }
}
