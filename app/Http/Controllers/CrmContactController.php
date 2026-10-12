<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\User;
use App\Models\ContactNote;
use App\Models\Deal;
use App\Models\SupportTicket;
use App\Models\Shipment;
use App\Models\CommunicationLog;
use App\Services\PacraCompanyService;

class CrmContactController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    /* ─────────── Companies ─────────── */

    public function companies(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $query = Company::withCount(['contacts', 'deals', 'tickets']);
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $companies = $query->latest()->paginate(20);
        return view('admin.crm.companies.index', compact('companies'));
    }

    public function createCompany()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $agents = User::where('is_admin', true)->get();
        return view('admin.crm.companies.create', compact('agents'));
    }

    public function verifyPacra(Request $request, PacraCompanyService $pacra)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate(['search' => 'required|string|min:2|max:150']);

        try {
            return response()->json(['results' => $pacra->search($validated['search'])]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'PACRA could not be reached right now.'], 502);
        }
    }

    public function storeCompany(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'annual_revenue' => 'nullable|numeric',
            'employee_count' => 'nullable|integer',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_agent_id' => 'nullable|exists:users,id',
            'pacra_entity_number' => 'nullable|string|max:100',
            'pacra_entity_status' => 'nullable|string|max:100',
            'pacra_business_nature' => 'nullable|string|max:255',
        ]);
        if (!empty($validated['pacra_entity_number'])) $validated['pacra_verified_at'] = now();
        Company::create($validated);
        return redirect()->route('admin.crm.companies')->with('success', 'Company created successfully.');
    }

    public function showCompany(Company $company)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $company->load(['contacts', 'deals.stage', 'tickets', 'assignedAgent']);
        return view('admin.crm.companies.show', compact('company'));
    }

    public function editCompany(Company $company)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $agents = User::where('is_admin', true)->get();
        $company->load('contacts');
        $availableContacts = User::where('is_admin', false)->whereDoesntHave('companies', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->get();
        return view('admin.crm.companies.edit', compact('company', 'agents', 'availableContacts'));
    }

    public function updateCompany(Request $request, Company $company)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'annual_revenue' => 'nullable|numeric',
            'employee_count' => 'nullable|integer',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
            'assigned_agent_id' => 'nullable|exists:users,id',
            'pacra_entity_number' => 'nullable|string|max:100',
            'pacra_entity_status' => 'nullable|string|max:100',
            'pacra_business_nature' => 'nullable|string|max:255',
        ]);
        if (!empty($validated['pacra_entity_number'])) $validated['pacra_verified_at'] = now();
        $company->update($validated);
        return redirect()->route('admin.crm.companies')->with('success', 'Company updated successfully.');
    }

    public function linkContact(Request $request, Company $company)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string',
            'is_primary' => 'nullable|boolean',
        ]);
        $company->contacts()->syncWithoutDetaching([
            $request->user_id => ['role' => $request->role, 'is_primary' => $request->boolean('is_primary', false)]
        ]);
        return back()->with('success', 'Contact linked successfully.');
    }

    public function unlinkContact(Company $company, User $user)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $company->contacts()->detach($user->id);
        return back()->with('success', 'Contact unlinked.');
    }

    /* ─────────── 360-Degree Contact View ─────────── */

    public function contact360(User $user)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $user->load(['company', 'companies', 'shipments', 'deals.stage', 'tasks', 'tickets', 'notes.creator', 'communicationLogs', 'messagingLogs']);

        $shipments = $user->shipments()->latest()->get();
        $totalSpent = $shipments->sum('cost');
        $lifetimeValue = $totalSpent;

        $notes = $user->notes()->latest()->paginate(10);
        $activities = $this->buildActivityTimeline($user);

        $preferences = $user->preferences ?? [];

        return view('admin.crm.contacts.show360', compact('user', 'shipments', 'lifetimeValue', 'notes', 'activities', 'preferences'));
    }

    public function storeNote(Request $request, User $user)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'type' => 'required|string|in:note,call,email,meeting,purchase,preference',
            'content' => 'required|string',
            'metadata' => 'nullable|string',
        ]);
        $validated['contact_id'] = $user->id;
        $validated['created_by'] = Auth::id();
        if (!empty($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata'], true) ?? [];
        }
        ContactNote::create($validated);
        return back()->with('success', 'Note added successfully.');
    }

    private function buildActivityTimeline(User $user)
    {
        $timeline = collect();

        foreach ($user->shipments as $s) {
            $timeline->push([
                'date' => $s->created_at,
                'type' => 'purchase',
                'icon' => 'fa-box',
                'color' => '#4caf50',
                'title' => 'Shipment Created',
                'description' => "{$s->serial_no} - {$s->origin} to {$s->destination}",
            ]);
        }

        foreach ($user->communicationLogs as $log) {
            $channel = $log->channel ?? $log->type ?? 'message';
            $direction = $log->direction ?? 'outgoing';
            $timeline->push([
                'date' => $log->created_at,
                'type' => 'communication',
                'icon' => $channel === 'sms' ? 'fa-comment-sms' : ($channel === 'whatsapp' ? 'fa-whatsapp' : 'fa-envelope'),
                'color' => $channel === 'sms' ? '#ff6200' : ($channel === 'whatsapp' ? '#007f7f' : '#3b82f6'),
                'title' => ucfirst($channel) . ' ' . ucfirst($direction),
                'description' => Str::limit($log->message, 80),
            ]);
        }

        foreach ($user->messagingLogs as $log) {
            $channel = $log->channel ?? 'message';
            $direction = $log->direction ?? 'outgoing';
            $timeline->push([
                'date' => $log->created_at,
                'type' => 'communication',
                'icon' => $channel === 'sms' ? 'fa-comment-sms' : 'fa-whatsapp',
                'color' => $channel === 'sms' ? '#ff6200' : '#007f7f',
                'title' => ucfirst($channel) . ' ' . ucfirst($direction),
                'description' => Str::limit($log->message, 80),
            ]);
        }

        foreach ($user->deals as $deal) {
            $timeline->push([
                'date' => $deal->created_at,
                'type' => 'deal',
                'icon' => 'fa-handshake',
                'color' => '#8b5cf6',
                'title' => 'Deal: ' . $deal->title,
                'description' => 'Value: ' . number_format($deal->value, 2) . ' ' . $deal->currency,
            ]);
        }

        foreach ($user->tickets as $ticket) {
            $timeline->push([
                'date' => $ticket->created_at,
                'type' => 'ticket',
                'icon' => 'fa-headset',
                'color' => '#ef4444',
                'title' => 'Support Ticket: ' . $ticket->subject,
                'description' => $ticket->status,
            ]);
        }

        return $timeline->sortByDesc('date')->values();
    }
}
