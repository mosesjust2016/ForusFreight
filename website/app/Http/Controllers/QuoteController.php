<?php

// app/Http/Controllers/QuoteController.php
namespace App\Http\Controllers;

use App\Services\BrevoMailService;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function submit(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'service_type' => 'required|string',
            'full_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'pickup' => 'required|string',
            'delivery' => 'required|string',
            'weight' => 'nullable|numeric',
            'dimensions' => 'nullable|string',
            'details' => 'nullable|string'
        ]);
        
        $serviceType = $this->getServiceTypeName($validated['service_type']);
        
        $html = view('emails.quote-request', [
            'serviceType'  => $serviceType,
            'fullName'     => $validated['full_name'],
            'company'      => $validated['company'] ?? '',
            'email'        => $validated['email'],
            'phone'        => $validated['phone'],
            'pickup'       => $validated['pickup'],
            'delivery'     => $validated['delivery'],
            'weight'       => $validated['weight'] ?? '',
            'dimensions'   => $validated['dimensions'] ?? '',
            'details'      => $validated['details'] ?? '',
            'submittedAt'  => now()->format('Y-m-d H:i:s'),
        ])->render();
        
        try {
            app(BrevoMailService::class)->send(
                'info@forusfl.co.zm',
                'Forus Freight Quotes',
                'New Quote Request - ' . $serviceType . ' from ' . $validated['full_name'],
                $html
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Quote request submitted successfully! We will contact you within 2 hours.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'There was an error submitting your request. Please try again or contact us directly.'
            ], 500);
        }
    }
    
    /**
     * Get human-readable service type name
     */
    private function getServiceTypeName($type)
    {
        $types = [
            'same-day' => 'Same-Day Delivery',
            'cross-border' => 'Cross-Border Shipping',
            'warehousing' => 'Warehousing & Storage',
            'bulk-cargo' => 'Bulk Cargo Transport',
            'express' => 'Express Delivery',
            'other' => 'Other Services'
        ];
        
        return $types[$type] ?? $type;
    }
}
