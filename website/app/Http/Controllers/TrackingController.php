<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    /**
     * Show the tracking page (public) - handles both /track and /tracking
     */
    public function show(Request $request)
    {
        // Check if tracking number is in query string
        $trackingNumber = $request->query('tracking_number') ?? session('tracking_attempt');
        
        // If user is authenticated and has a tracking attempt, show their shipment
        if (Auth::check() && $trackingNumber) {
            $shipment = Shipment::where('tracking_number', $trackingNumber)
                ->with('trackingEvents')
                ->first();
                
            if ($shipment) {
                return view('tracking', compact('shipment'));
            }
        }
        
        return view('tracking');
    }

    /**
     * Process tracking number check
     */
    public function check(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string|min:5'
        ]);

        $trackingNumber = $request->tracking_number;

        $shipment = Shipment::where('tracking_number', $trackingNumber)
            ->with('trackingEvents')
            ->first();

        if (!$shipment) {
            return back()->with('error', 'Tracking number not found. Please check and try again.')
                ->withInput();
        }

        return view('tracking', compact('shipment'));
    }

    /**
     * Show individual tracking details (protected)
     */
    public function showTracking($tracking_number)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $shipment = Shipment::where('tracking_number', $tracking_number)
            ->with('trackingEvents')
            ->first();

        if (!$shipment) {
            return redirect()->route('track')->with('error', 'Tracking number not found.');
        }

        // Clear the tracking attempt from session
        session()->forget('tracking_attempt');

        return view('tracking', compact('shipment'));
    }

    /**
     * User dashboard showing all their shipments (protected)
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $shipments = Shipment::where('user_id', $user->id)
            ->with('trackingEvents')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', compact('shipments'));
    }
}