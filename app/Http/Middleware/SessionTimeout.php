<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SessionTimeout
{
    /**
     * Session timeout duration in seconds (3 minutes = 180 seconds)
     */
    protected const TIMEOUT_SECONDS = 180;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (!session()->has('user') || !session('user.authenticated')) {
            return $next($request);
        }

        $lastActivity = session('last_activity_time');
        $currentTime = Carbon::now()->timestamp;

        // Check if session has timed out
        if ($lastActivity && ($currentTime - $lastActivity) > self::TIMEOUT_SECONDS) {
            // Log the session timeout
            \Log::info("Session timeout for user: " . session('user.email') . ". Last activity: " . 
                Carbon::createFromTimestamp($lastActivity)->toDateTimeString());

            // Clear the session
            session()->forget('user');
            session()->forget('last_activity_time');
            session()->flush();

            // If it's an AJAX request, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'session_expired' => true,
                    'message' => 'Your session has expired due to inactivity. Please log in again.',
                    'redirect' => route('login')
                ], 401);
            }

            // Redirect to login with session expired message
            return redirect()->route('login')->with('session_expired', 'Your session has expired due to inactivity. Please log in again.');
        }

        // Update last activity time
        session(['last_activity_time' => $currentTime]);

        return $next($request);
    }
}
