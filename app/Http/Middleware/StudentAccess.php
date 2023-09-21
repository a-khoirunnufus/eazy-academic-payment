<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\HasHomepage;

class StudentAccess
{
    use HasHomepage;

    public function handle(Request $request, Closure $next): Response
    {
        if(!auth()->user()->hasAssociateData("student"))
            return redirect($this->getHompagePath());

        return $next($request);
    }
}
