<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFullyVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->generateEmailOtp();
            return redirect()->route('verification.notice');
        }

        if (! $user->hasVerifiedPhone()) {
            $user->generatePhoneOtp();
            return redirect()->route('verification.phone');
        }

        return $next($request);
    }
}
