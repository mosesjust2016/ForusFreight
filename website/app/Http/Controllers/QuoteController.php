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
        
        // Here you would typically:
        // 1. Save to database
        // 2. Send email notification
        // 3. Send confirmation email to customer
        
        // For now, just return success response
        return response()->json([
            'success' => true,
            'message' => 'Quote request submitted successfully!'
        ]);
    }
}