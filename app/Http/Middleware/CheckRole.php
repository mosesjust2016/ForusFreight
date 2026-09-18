<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->is_admin) {
            return $next($request);
        }

        if (! $user->hasAnyRole($roles)) {
            // A denied staff member belongs back on their own admin dashboard,
            // not the client-facing one — route('dashboard') sits behind the
            // 'web' guard, which a staff-only account was never logged into.
            $redirectRoute = $user->isStaff() ? 'admin.dashboard' : 'dashboard';

            return redirect()->route($redirectRoute)->with('error', 'Access denied. You do not have the required role.');
        }

        return $next($request);
    }
}
