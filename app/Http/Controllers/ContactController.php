<?php

namespace App\Http\Controllers;

use App\Services\BrevoMailService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:30',
            'message' => 'required|string|max:5000',
            'consent' => 'required|accepted',
        ]);

        $html = view('emails.contact-request', [
            'fullName' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'messageBody' => $validated['message'],
            'submittedAt' => now()->format('Y-m-d H:i:s'),
        ])->render();

        try {
            app(BrevoMailService::class)->send(
                'info@forusfl.co.zm',
                'Forus Freight Contact',
                'New Contact Form Message from ' . $validated['full_name'],
                $html
            );

            return response()->json([
                'success' => true,
                'message' => 'Thank you for reaching out! Our team will respond within 24 hours.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'There was an error sending your message. Please try again or contact us directly.',
            ], 500);
        }
    }
}
