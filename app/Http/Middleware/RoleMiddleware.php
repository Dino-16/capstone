<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!session()->has('user') || !session('user.authenticated')) {
            return redirect()->route('login');
        }

        $userPosition = trim(session('user.position'));

        $isAuthorized = false;
        foreach ($roles as $role) {
            if (strcasecmp($userPosition, trim($role)) === 0) {
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized) {
            // Log unauthorized access attempt if needed
            \Log::warning("Unauthorized access attempt by {$userPosition} to " . $request->fullUrl());
            
            return response()->view('errors.403', [], 403);
            // Alternative: return redirect()->route('dashboard')->with('error', 'You do not have permission to access that page.');
        }

        return $next($request);
    }
}
