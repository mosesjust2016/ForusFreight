<?php

// app/Http/Controllers/QuoteController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        
        // Prepare email content
        $serviceType = $this->getServiceTypeName($validated['service_type']);
        
        $emailBody = "NEW QUOTE REQUEST - Forus Freight\n";
        $emailBody .= "========================================\n\n";
        $emailBody .= "Service Type: {$serviceType}\n";
        $emailBody .= "Full Name: {$validated['full_name']}\n";
        $emailBody .= "Company: " . ($validated['company'] ?? 'N/A') . "\n";
        $emailBody .= "Email: {$validated['email']}\n";
        $emailBody .= "Phone: {$validated['phone']}\n\n";
        $emailBody .= "PICKUP & DELIVERY DETAILS\n";
        $emailBody .= "-------------------------\n";
        $emailBody .= "Pickup Location: {$validated['pickup']}\n";
        $emailBody .= "Delivery Location: {$validated['delivery']}\n";
        $emailBody .= "Weight: " . ($validated['weight'] ? $validated['weight'] . ' kg' : 'Not specified') . "\n";
        $emailBody .= "Dimensions: " . ($validated['dimensions'] ?? 'Not specified') . "\n\n";
        $emailBody .= "ADDITIONAL DETAILS\n";
        $emailBody .= "------------------\n";
        $emailBody .= ($validated['details'] ?? 'No additional details provided.') . "\n\n";
        $emailBody .= "Submitted on: " . now()->format('Y-m-d H:i:s') . "\n";
        
        // Send email to info@forusfl.co.zm
        try {
            Mail::raw($emailBody, function ($message) use ($validated, $serviceType) {
                $message->from('noreply@forusfl.co.zm', 'Forus Freight Quotes')
                        ->to('info@forusfl.co.zm')
                        ->subject('New Quote Request - ' . $serviceType . ' from ' . $validated['full_name']);
            });
            
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
