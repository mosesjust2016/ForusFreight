<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Task;
use App\Models\Document;
use App\Models\Company;
use App\Models\User;

class CrmSalesController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    /* ─────────── Deal Pipeline ─────────── */

    public function pipeline(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $stages = DealStage::orderBy('position')->get();
        $query = Deal::with(['company', 'contact', 'assignedTo', 'stage'])->distinct();

        if ($request->has('stage')) {
            $query->where('deal_stage_id', $request->stage);
        }
        if ($request->has('agent')) {
            $query->where('assigned_to', $request->agent);
        }
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        $deals = $query->latest()->paginate(30);
        $agents = User::where('is_admin', true)->get();

        $pipelineStats = [];
        foreach ($stages as $stage) {
            $pipelineStats[$stage->id] = [
                'name' => $stage->name,
                'count' => Deal::where('deal_stage_id', $stage->id)->count(),
                'value' => Deal::where('deal_stage_id', $stage->id)->sum('value'),
                'color' => $stage->color,
            ];
        }

        return view('admin.crm.sales.pipeline', compact('stages', 'deals', 'agents', 'pipelineStats'));
    }

    public function createDeal()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $stages = DealStage::orderBy('position')->get();
        $companies = Company::all();
        $contacts = User::where('is_admin', false)->get();
        $agents = User::where('is_admin', true)->get();
        return view('admin.crm.sales.deals.create', compact('stages', 'companies', 'contacts', 'agents'));
    }

    public function storeDeal(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'nullable|exists:companies,id',
            'contact_id' => 'nullable|exists:users,id',
            'assigned_to' => 'nullable|exists:users,id',
            'deal_stage_id' => 'required|exists:deal_stages,id',
            'value' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'source' => 'nullable|string',
            'priority' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        Deal::create($validated);
        return redirect()->route('admin.crm.pipeline')->with('success', 'Deal created successfully.');
    }

    public function showDeal(Deal $deal)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $deal->load(['company', 'contact', 'assignedTo', 'stage', 'tasks.assignedTo', 'documents']);
        return view('admin.crm.sales.deals.show', compact('deal'));
    }

    public function updateDealStage(Request $request, Deal $deal)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $request->validate(['deal_stage_id' => 'required|exists:deal_stages,id']);
        $deal->update(['deal_stage_id' => $request->deal_stage_id]);

        $stage = DealStage::find($request->deal_stage_id);
        if ($stage && $stage->is_closed) {
            $deal->update(['actual_close_date' => now()]);
        }

        return back()->with('success', 'Deal stage updated.');
    }

    /* ─────────── Deal Stages Management ─────────── */

    public function stages()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $stages = DealStage::orderBy('position')->get();
        return view('admin.crm.sales.stages.index', compact('stages'));
    }

    public function storeStage(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string',
            'position' => 'nullable|integer',
            'win_probability' => 'nullable|numeric|min:0|max:100',
            'is_closed' => 'nullable|boolean',
            'is_won' => 'nullable|boolean',
        ]);
        DealStage::create($validated);
        return back()->with('success', 'Stage created.');
    }

    /* ─────────── Tasks ─────────── */

    public function tasks(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $query = Task::with(['assignedTo', 'contact', 'deal']);
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('mine')) {
            $query->where('assigned_to', Auth::id());
        }

        $tasks = $query->latest()->paginate(25);
        $agents = User::where('is_admin', true)->get();
        $contacts = User::where('is_admin', false)->get();
        $deals = Deal::all();

        $stats = [
            'total' => Task::count(),
            'pending' => Task::where('status', 'pending')->count(),
            'overdue' => Task::overdue()->count(),
            'completed' => Task::where('status', 'completed')->count(),
        ];

        return view('admin.crm.sales.tasks.index', compact('tasks', 'agents', 'contacts', 'deals', 'stats'));
    }

    public function storeTask(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'deal_id' => 'nullable|exists:deals,id',
            'contact_id' => 'nullable|exists:users,id',
            'type' => 'required|string',
            'due_at' => 'nullable|date',
        ]);
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';
        Task::create($validated);
        return back()->with('success', 'Task created successfully.');
    }

    public function completeTask(Task $task)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        return back()->with('success', 'Task marked as completed.');
    }

    /* ─────────── Documents (Quotes & Proposals) ─────────── */

    public function documents(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $query = Document::with(['contact', 'deal', 'creator']);
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        $documents = $query->latest()->paginate(20);
        return view('admin.crm.sales.documents.index', compact('documents'));
    }

    public function storeDocument(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|string',
            'deal_id' => 'nullable|exists:deals,id',
            'contact_id' => 'nullable|exists:users,id',
            'content' => 'nullable|string',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'expires_at' => 'nullable|date',
        ]);
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';
        Document::create($validated);
        return back()->with('success', 'Document created.');
    }

    public function sendDocument(Document $document)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $document->update(['status' => 'sent', 'sent_at' => now()]);
        return back()->with('success', 'Document marked as sent.');
    }

    /* ─────────── Forecasting ─────────── */

    public function forecast()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $currentMonth = Carbon::now()->startOfMonth();
        $next3Months = collect();
        for ($i = 0; $i < 3; $i++) {
            $month = $currentMonth->copy()->addMonths($i);
            $deals = Deal::where('expected_close_date', '>=', $month->copy()->startOfMonth())
                ->where('expected_close_date', '<=', $month->copy()->endOfMonth())
                ->with('stage')
                ->get();

            $weighted = $deals->sum(function ($d) {
                return $d->value * ($d->stage->win_probability / 100);
            });
            $total = $deals->sum('value');

            $next3Months->push([
                'month' => $month->format('F Y'),
                'deal_count' => $deals->count(),
                'total_value' => $total,
                'weighted_forecast' => $weighted,
            ]);
        }

        $wonRevenue = Deal::whereNotNull('actual_close_date')
            ->whereYear('actual_close_date', Carbon::now()->year)
            ->whereHas('stage', function ($q) { $q->where('is_won', true); })
            ->sum('value');

        $pipelineTotal = Deal::whereHas('stage', function ($q) { $q->where('is_closed', false); })->sum('value');

        $agentPerformance = User::where('is_admin', true)
            ->withSum('assignedDeals as total_pipeline', 'value')
            ->withCount('assignedDeals')
            ->get();

        return view('admin.crm.sales.forecast', compact('next3Months', 'wonRevenue', 'pipelineTotal', 'agentPerformance'));
    }

    /* ─────────── Lead Routing / Scoring (Manual) ─────────── */

    public function leads()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $leads = User::where('is_admin', false)
            ->where('crm_status', 'lead')
            ->withCount('shipments')
            ->latest()
            ->paginate(25);

        $agents = User::where('is_admin', true)->get();
        return view('admin.crm.sales.leads', compact('leads', 'agents'));
    }

    public function assignLead(Request $request, User $user)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $request->validate(['assigned_agent' => 'required|exists:users,id']);
        $user->update([
            'assigned_agent' => User::find($request->assigned_agent)->name,
            'crm_status' => 'active',
        ]);
        return back()->with('success', 'Lead assigned and activated.');
    }
}
