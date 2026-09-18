<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityObservability
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Observability Logic
        $user = $request->user();
        $path = $request->path();
        
        // Alert on sensitive data exports
        if (str_contains($path, 'admin/reports/export')) {
            $type = $request->get('type', 'unknown');
            \App\Services\ObservabilityService::pushAlert(
                "Sensitive Data Export: $type report downloaded by " . ($user ? $user->email : 'Guest') . ". \n\n" .
                "IP Address: " . $request->ip(),
                'INFO'
            );
        }

        // REAL intrusion: authenticated non-admin user trying to reach admin routes
        if (str_starts_with($path, 'admin/') && $user && !$user->is_admin) {
            \App\Services\ObservabilityService::intrusionDetected(
                "Unauthorized admin access attempt.\n\n" .
                "User: {$user->email} (non-admin account)\n" .
                "Route: /{$path}\n" .
                "IP: " . $request->ip()
            );
        }

        // REAL intrusion: unauthenticated request that bypassed auth middleware
        if (str_starts_with($path, 'admin/') && !$user && $response->getStatusCode() === 200) {
            \App\Services\ObservabilityService::intrusionDetected(
                "Unauthenticated request reached a protected admin route.\n\n" .
                "Route: /{$path}\n" .
                "IP: " . $request->ip()
            );
        }

        // Legitimate admin session start — log as SECURITY info, not intrusion
        if ($path === 'admin/dashboard' && $user && $user->is_admin && !session()->has('admin_notified')) {
            \App\Services\ObservabilityService::pushAlert(
                "Admin session started.\n\n" .
                "User: {$user->email}\n" .
                "IP: " . $request->ip(),
                'SECURITY'
            );
            session(['admin_notified' => true]);
        }

        return $response;
    }
}
