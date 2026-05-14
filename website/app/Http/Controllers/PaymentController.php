<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('user_id', Auth::id())
            ->with('shipment')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.invoices.index', compact('invoices'));
    }

    public function show($invoice_number)
    {
        $invoice = Invoice::where('user_id', Auth::id())
            ->where('invoice_number', $invoice_number)
            ->with('shipment')
            ->firstOrFail();

        return view('client.invoices.show', compact('invoice'));
    }

    public function checkout($invoice_number)
    {
        $invoice = Invoice::where('user_id', Auth::id())
            ->where('invoice_number', $invoice_number)
            ->firstOrFail();

        if ($invoice->status === 'paid') {
            return redirect()->back()->with('info', 'This invoice is already paid.');
        }

        return view('client.payments.checkout', compact('invoice'));
    }
}
