<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\QuoteRequest;
use App\Models\SupportTicket;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CrmHomeController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()?->isStaff(), 403);

        $openDeals = Deal::whereHas('stage', fn ($query) => $query->where('is_closed', false));

        $stats = [
            'contacts' => User::where('is_admin', false)->count(),
            'leads' => User::where('is_admin', false)->where('crm_status', 'lead')->count(),
            'openDeals' => (clone $openDeals)->count(),
            'pipelineValue' => (float) (clone $openDeals)->sum('value'),
            'newQuotes' => QuoteRequest::where('status', 'new')->count(),
            'followUps' => Task::pending()->whereNotNull('due_at')->where('due_at', '<=', now()->endOfDay())->count(),
            'openTickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
        ];

        $quoteRequests = QuoteRequest::with('assignee')->where('status', 'new')->latest()->take(6)->get();
        $followUps = Task::with(['contact', 'deal'])->pending()->orderByRaw('due_at IS NULL')->orderBy('due_at')->take(6)->get();
        $leads = User::where('is_admin', false)->where('crm_status', 'lead')->latest('last_engagement_at')->take(6)->get();
        $tickets = SupportTicket::with('contact')->whereIn('status', ['open', 'in_progress'])->latest()->take(6)->get();

        return view('admin.crm.home', compact('stats', 'quoteRequests', 'followUps', 'leads', 'tickets'));
    }
}
