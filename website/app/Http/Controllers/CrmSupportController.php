<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\KnowledgeBaseArticle;
use App\Models\User;
use App\Models\Company;

class CrmSupportController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    public function tickets(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $query = SupportTicket::with(['contact', 'company', 'assignedTo']);
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->has('mine')) {
            $query->where('assigned_to', Auth::id());
        }

        $tickets = $query->latest()->paginate(25);
        $agents = User::where('is_admin', true)->get();

        $stats = [
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'urgent' => SupportTicket::where('priority', 'urgent')->whereIn('status', ['open', 'in_progress'])->count(),
        ];

        return view('admin.crm.support.tickets', compact('tickets', 'agents', 'stats'));
    }

    public function showTicket(SupportTicket $ticket)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $ticket->load(['contact', 'company', 'assignedTo', 'replies.user']);
        $agents = User::where('is_admin', true)->get();
        return view('admin.crm.support.ticket_show', compact('ticket', 'agents'));
    }

    public function storeTicket(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_id' => 'nullable|exists:users,id',
            'company_id' => 'nullable|exists:companies,id',
            'assigned_to' => 'nullable|exists:users,id',
            'channel' => 'required|string',
            'priority' => 'required|string',
            'category' => 'nullable|string',
        ]);
        SupportTicket::create($validated);
        return redirect()->route('admin.crm.tickets')->with('success', 'Ticket created.');
    }

    public function replyTicket(Request $request, SupportTicket $ticket)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'nullable|boolean',
        ]);
        $validated['support_ticket_id'] = $ticket->id;
        $validated['user_id'] = Auth::id();
        TicketReply::create($validated);

        if (!$request->boolean('is_internal', false)) {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Reply added.');
    }

    public function updateTicketStatus(Request $request, SupportTicket $ticket)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $request->validate(['status' => 'required|string']);
        $update = ['status' => $request->status];
        if ($request->status === 'resolved') {
            $update['resolved_at'] = now();
        }
        $ticket->update($update);
        return back()->with('success', 'Ticket status updated.');
    }

    public function assignTicket(Request $request, SupportTicket $ticket)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        $ticket->update(['assigned_to' => $request->assigned_to, 'status' => 'in_progress']);
        return back()->with('success', 'Ticket assigned.');
    }

    /* ─────────── Knowledge Base ─────────── */

    public function knowledgeBase(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $query = KnowledgeBaseArticle::with('author');
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $articles = $query->latest()->paginate(20);
        $categories = KnowledgeBaseArticle::select('category')->distinct()->pluck('category');
        return view('admin.crm.support.knowledge_base', compact('articles', 'categories'));
    }

    public function storeArticle(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:knowledge_base_articles,slug',
            'content' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|string',
            'is_internal' => 'nullable|boolean',
        ]);
        $validated['author_id'] = Auth::id();
        KnowledgeBaseArticle::create($validated);
        return back()->with('success', 'Article published.');
    }

    public function updateArticle(Request $request, KnowledgeBaseArticle $article)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|string',
        ]);
        $article->update($validated);
        return back()->with('success', 'Article updated.');
    }
}
