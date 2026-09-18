<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->hasVerifiedPhone()) {
            return redirect()->route('phone.verify')
                ->with('info', 'Please verify your phone number to continue.');
        }

        return $next($request);
    }
}
