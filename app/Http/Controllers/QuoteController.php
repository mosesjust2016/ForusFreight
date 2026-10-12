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
            'service_type' => 'required|string|in:road-freight,air-freight,sea-freight,china-shopping,customs,same-day,cross-border,warehousing,bulk-cargo,express,other',
            'full_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'pickup' => 'required|string|max:255',
            'delivery' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0|max:1000000',
            'dimensions' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:5000'
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
                'info@forusfreight.com',
                'Forus Freight Quotes',
                'New Quote Request - ' . $serviceType . ' from ' . $validated['full_name'],
                $html
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Quote request submitted successfully! We will contact you soon.'
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
            'road-freight' => 'Road Freight',
            'air-freight' => 'Air Freight',
            'sea-freight' => 'Sea Freight',
            'china-shopping' => 'China Orders to Lusaka',
            'customs' => 'Customs Clearance',
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
