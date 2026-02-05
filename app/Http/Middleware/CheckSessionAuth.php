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

        // Role-based dashboard redirection
        if ($request->is('dashboard') || $request->is('admin/dashboard') || $request->is('admin/superadmin/dashboard')) {
            $position = trim(session('user.position'));
            $currentRoute = $request->route()->getName();

            $itRoles = ['Super Admin'];
            $isIT = false;
            foreach ($itRoles as $role) {
                if (strcasecmp($position, $role) === 0) {
                    $isIT = true;
                    break;
                }
            }

            if ($isIT && $currentRoute !== 'superadmin.dashboard') {
                return redirect()->route('superadmin.dashboard');
            } elseif (strcasecmp($position, 'HR Manager') === 0 && $currentRoute !== 'admin.dashboard') {
                return redirect()->route('admin.dashboard');
            } elseif (strcasecmp($position, 'HR Staff') === 0 && $currentRoute !== 'dashboard') {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
