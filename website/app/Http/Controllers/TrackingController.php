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
        // Check if serial_no is in query string
        $serialNo = $request->query('serial_no') ?? session('tracking_attempt');
        
        // If user is authenticated and has a tracking attempt, show their shipment
        if (Auth::check() && $serialNo) {
            $shipment = Shipment::where('serial_no', $serialNo)
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
            'serial_no' => 'required|string|min:3'
        ]);

        $serialNo = $request->serial_no;

        $shipment = Shipment::where('serial_no', $serialNo)
            ->with('trackingEvents')
            ->first();

        if (!$shipment) {
            return back()->with('error', 'Serial number not found. Please check and try again.')
                ->withInput();
        }

        return view('tracking', compact('shipment'));
    }

    /**
     * Show individual tracking details (protected)
     */
    public function showTracking($serial_no)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $shipment = Shipment::where('serial_no', $serial_no)
            ->with('trackingEvents')
            ->first();

        if (!$shipment) {
            return redirect()->route('track')->with('error', 'Serial number not found.');
        }

        // Verify the user owns this shipment (or is admin)
        if (!$user->is_admin && $shipment->user_id !== $user->id) {
            return redirect()->route('client.shipments')
                ->with('error', 'You do not have permission to view this shipment.');
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