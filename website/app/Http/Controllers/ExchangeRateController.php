<?php

namespace App\Http\Controllers;

use App\Models\CurrencyHedge;
use App\Models\ExchangeRate;
use App\Models\Shipment;
use App\Models\User;
use App\Services\BozExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExchangeRateController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        return null;
    }

    /**
     * Display exchange rates dashboard.
     */
    public function index()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $latestRate = ExchangeRate::latest('recorded_at')->first();
        $rates = ExchangeRate::latest('recorded_at')->paginate(30);
        $hedges = CurrencyHedge::with('user', 'shipment')->latest()->paginate(20);

        return view('admin.exchange-rates.index', compact('latestRate', 'rates', 'hedges'));
    }

    /**
     * Sync rates from BOZ API.
     */
    public function sync()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $service = new BozExchangeRateService();
        $rate = $service->sync();

        if ($rate) {
            return back()->with('success', "Exchange rate synced: 1 USD = {$rate->mid_rate} ZMW (BOZ)");
        }

        return back()->with('error', 'Failed to fetch exchange rate from BOZ. Please try again later.');
    }

    /**
     * Show hedging form.
     */
    public function createHedge()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $clients = User::where('is_admin', false)->get();
        $shipments = Shipment::whereDoesntHave('hedge')->orWhereHas('hedge', function ($q) {
            $q->whereIn('status', ['expired', 'cancelled']);
        })->latest()->get();

        $latestRate = ExchangeRate::latest('recorded_at')->first();

        return view('admin.exchange-rates.create-hedge', compact('clients', 'shipments', 'latestRate'));
    }

    /**
     * Store a new hedge.
     */
    public function storeHedge(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shipment_id' => 'nullable|exists:shipments,id',
            'amount_usd' => 'required|numeric|min:0.01',
            'hedged_rate' => 'required|numeric|min:0.01',
            'hedge_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:hedge_date',
            'notes' => 'nullable|string',
        ]);

        $amountZmw = round($validated['amount_usd'] * $validated['hedged_rate'], 2);

        CurrencyHedge::create([
            'user_id' => $validated['user_id'],
            'shipment_id' => $validated['shipment_id'],
            'amount_usd' => $validated['amount_usd'],
            'hedged_rate' => $validated['hedged_rate'],
            'amount_zmw' => $amountZmw,
            'hedge_date' => $validated['hedge_date'],
            'expiry_date' => $validated['expiry_date'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.exchange-rates')->with('success', 'Currency hedge created successfully!');
    }

    /**
     * Cancel a hedge.
     */
    public function cancelHedge(CurrencyHedge $hedge)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        if ($hedge->status !== 'active') {
            return back()->with('error', 'Only active hedges can be cancelled.');
        }

        $hedge->update(['status' => 'cancelled']);

        return back()->with('success', 'Hedge cancelled successfully.');
    }

    /**
     * Utilize a hedge against a shipment.
     */
    public function utilizeHedge(Request $request, CurrencyHedge $hedge)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        if ($hedge->status !== 'active') {
            return back()->with('error', 'Only active hedges can be utilized.');
        }

        $validated = $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
        ]);

        $hedge->update([
            'shipment_id' => $validated['shipment_id'],
            'status' => 'utilized',
        ]);

        return back()->with('success', 'Hedge utilized for shipment successfully.');
    }

    /**
     * Get current rate as JSON (API endpoint).
     */
    public function currentRate()
    {
        $rate = ExchangeRate::latest('recorded_at')->first();

        if (! $rate) {
            return response()->json(['error' => 'No exchange rate available'], 404);
        }

        return response()->json([
            'base_currency' => $rate->base_currency,
            'quote_currency' => $rate->quote_currency,
            'buying_rate' => $rate->buying_rate,
            'mid_rate' => $rate->mid_rate,
            'selling_rate' => $rate->selling_rate,
            'recorded_at' => $rate->recorded_at->toDateTimeString(),
        ]);
    }
}
