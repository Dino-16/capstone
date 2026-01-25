<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user') || !session('user.authenticated')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
