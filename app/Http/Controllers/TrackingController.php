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
        // Check query string for either tracking_number or serial_no
        $trackingNumber = $request->query('tracking_number')
            ?? $request->query('serial_no')
            ?? session('tracking_attempt');

        // If user is authenticated and has a tracking attempt, show their shipment
        if (Auth::check() && $trackingNumber) {
            $trackingNumber = trim($trackingNumber);
            $shipment = Shipment::where('tracking_number', $trackingNumber)
                ->orWhere('serial_no', $trackingNumber)
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
            'tracking_number' => 'required|string|min:3'
        ]);

        $trackingNumber = trim($request->tracking_number);

        $shipment = Shipment::where('tracking_number', $trackingNumber)
            ->orWhere('serial_no', $trackingNumber)
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

        $user = Auth::user();
        // The client shipments/invoices lists link here using the shipment's
        // serial_no (the identifier shown to clients there) — nearly half of
        // all shipments have no tracking_number at all, so this must match
        // either field or those links 404 into "not found" for those clients.
        $shipment = Shipment::where('tracking_number', $tracking_number)
            ->orWhere('serial_no', $tracking_number)
            ->with('trackingEvents')
            ->first();

        if (!$shipment) {
            return redirect()->route('track')->with('error', 'Tracking number not found.');
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
