<?php

// app/Http/Controllers/QuoteController.php
namespace App\Http\Controllers;

use App\Services\BrevoMailService;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function estimate(Request $request)
    {
        $data = $request->validate([
            'service_type' => 'required|in:road-freight,air-freight,sea-freight,same-day,express,china-shopping',
            'pickup' => 'required|string|max:255',
            'delivery' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.1|max:1000000',
            'dimensions' => 'nullable|string|max:255',
        ]);

        $rate = config('forus.quote_rates.' . $data['service_type']);
        $subtotal = $rate['base'] + ((float) $data['weight'] * $rate['per_kg']);

        return response()->json([
            'success' => true,
            'currency' => 'ZMW',
            'min' => round($subtotal * 0.9, 2),
            'max' => round($subtotal * 1.15, 2),
            'transit_time' => $rate['days'],
            'disclaimer' => 'Indicative estimate. Final pricing depends on the confirmed route, cargo type, dimensions and applicable duties.',
        ]);
    }

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

        $quoteRequest = QuoteRequest::create($validated);
        
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
            report($e);
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
