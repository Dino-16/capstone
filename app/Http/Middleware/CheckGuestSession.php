<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGuestSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('user') && session('user.authenticated')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
