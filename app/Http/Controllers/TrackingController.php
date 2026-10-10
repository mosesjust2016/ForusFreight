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
        // Failures always land on the tracking page, never back(): the search
        // box also lives on the homepage (which can't show the error) and on
        // the results page, whose URL only accepts POST (back() would 405).
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'tracking_number' => 'required|string|min:3'
        ]);

        if ($validator->fails()) {
            return redirect()->route('tracking')->withErrors($validator)->withInput();
        }

        $trackingNumber = trim($request->tracking_number);

        $shipment = Shipment::where('tracking_number', $trackingNumber)
            ->orWhere('serial_no', $trackingNumber)
            ->with('trackingEvents')
            ->first();

        if (!$shipment) {
            return redirect()->route('tracking')
                ->with('error', 'We couldn’t find a shipment with that number. Please check it and try again, or contact us on WhatsApp.')
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

        // Shown inside the client portal (the public /tracking page stays for guests)
        return view('client.shipment', compact('shipment'));
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
