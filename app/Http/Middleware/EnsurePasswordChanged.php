<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Blocks every admin-portal route until a staff member created with a
     * system-generated temporary password has set their own. Without this,
     * a leaked or reused temporary password stays valid indefinitely.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            return redirect()->route('admin.force-password-change');
        }

        return $next($request);
    }
}
