<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToDashboardIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Admin and client are separate guards specifically so one browser
        // can hold both sessions at once. Only bounce away from the login/
        // register form once there's nothing left to log into — otherwise,
        // someone already signed in as a client (or admin) would never be
        // able to reach this form to add the other session.
        $clientAuthed = Auth::guard('web')->check();
        $adminAuthed = Auth::guard('admin')->check();

        if ($clientAuthed && $adminAuthed) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
