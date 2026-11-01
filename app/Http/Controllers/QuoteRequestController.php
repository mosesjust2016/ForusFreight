<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $requests = QuoteRequest::with('assignee')->when(
            in_array($status, ['new', 'contacted', 'quoted', 'closed'], true),
            fn ($query) => $query->where('status', $status)
        )->latest()->paginate(25)->withQueryString();

        return view('admin.quote-requests.index', compact('requests', 'status'));
    }

    public function update(Request $request, QuoteRequest $quoteRequest)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,quoted,closed'],
            'admin_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $quoteRequest->update([
            ...$data,
            'contacted_at' => $data['status'] === 'new' ? null : ($quoteRequest->contacted_at ?? now()),
        ]);

        return back()->with('success', 'Quote request updated.');
    }
}
