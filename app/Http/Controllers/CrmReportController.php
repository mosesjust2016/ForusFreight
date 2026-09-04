<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\SupportTicket;
use App\Models\Task;
use App\Models\User;
use App\Models\Company;
use App\Models\Campaign;
use App\Models\AnalyticsSnapshot;
use App\Models\Shipment;
use App\Models\ContactNote;

class CrmReportController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    public function dashboard()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $totalContacts = User::where('is_admin', false)->count();
        $totalCompanies = Company::count();
        $totalDeals = Deal::count();
        $openDeals = Deal::whereHas('stage', function ($q) { $q->where('is_closed', false); })->count();
        $pipelineValue = Deal::whereHas('stage', function ($q) { $q->where('is_closed', false); })->sum('value');
        $wonRevenue = Deal::whereHas('stage', function ($q) { $q->where('is_won', true); })->sum('value');

        $pendingTasks = Task::where('status', 'pending')->count();
        $overdueTasks = Task::overdue()->count();
        $openTickets = SupportTicket::whereIn('status', ['open', 'in_progress'])->count();

        $recentDeals = Deal::with(['contact', 'stage'])->latest()->take(5)->get();
        $recentTickets = SupportTicket::with('contact')->latest()->take(5)->get();

        $monthlyRevenue = Shipment::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('cost');

        $churnRisk = User::where('is_admin', false)
            ->where(function ($q) {
                $q->where('last_engagement_at', '<', Carbon::now()->subMonths(3))
                  ->orWhereNull('last_engagement_at');
            })
            ->whereDoesntHave('shipments', function ($q) {
                $q->where('created_at', '>', Carbon::now()->subMonths(3));
            })
            ->count();

        // AI-inspired insights (rule-based)
        $insights = $this->generateInsights();

        return view('admin.crm.reports.dashboard', compact(
            'totalContacts', 'totalCompanies', 'totalDeals', 'openDeals',
            'pipelineValue', 'wonRevenue', 'pendingTasks', 'overdueTasks',
            'openTickets', 'recentDeals', 'recentTickets', 'monthlyRevenue',
            'churnRisk', 'insights'
        ));
    }

    public function analytics(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $period = $request->get('period', 'month');
        $start = match ($period) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'quarter' => Carbon::now()->subMonths(3),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth(),
        };

        $dealsByStage = DealStage::withCount(['deals' => function ($q) use ($start) {
            $q->where('created_at', '>=', $start);
        }])->get();

        $agentPerformance = User::where('is_admin', true)
            ->withCount(['assignedDeals as deals_count' => function ($q) use ($start) {
                $q->where('created_at', '>=', $start);
            }])
            ->withSum(['assignedDeals as deals_value' => function ($q) use ($start) {
                $q->where('created_at', '>=', $start);
            }], 'value')
            ->get();

        $campaigns = Campaign::where('created_at', '>=', $start)->get();
        $ticketsByStatus = SupportTicket::selectRaw('status, count(*) as count')
            ->where('created_at', '>=', $start)
            ->groupBy('status')
            ->pluck('count', 'status');

        $contactGrowth = User::where('is_admin', false)
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.crm.reports.analytics', compact(
            'dealsByStage', 'agentPerformance', 'campaigns', 'ticketsByStatus', 'contactGrowth', 'period'
        ));
    }

    private function generateInsights(): array
    {
        $insights = [];

        $overdueTasks = Task::overdue()->count();
        if ($overdueTasks > 0) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fa-triangle-exclamation',
                'title' => 'Overdue Tasks',
                'message' => "You have {$overdueTasks} overdue task(s) requiring immediate attention.",
            ];
        }

        $urgentTickets = SupportTicket::where('priority', 'urgent')
            ->whereIn('status', ['open', 'in_progress'])
            ->count();
        if ($urgentTickets > 0) {
            $insights[] = [
                'type' => 'danger',
                'icon' => 'fa-fire',
                'title' => 'Urgent Tickets',
                'message' => "{$urgentTickets} urgent support ticket(s) are unresolved.",
            ];
        }

        $highValueDeals = Deal::where('value', '>', 50000)
            ->whereHas('stage', function ($q) { $q->where('is_closed', false); })
            ->where('expected_close_date', '<', Carbon::now()->addWeek())
            ->count();
        if ($highValueDeals > 0) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'fa-gem',
                'title' => 'High-Value Deals Closing Soon',
                'message' => "{$highValueDeals} high-value deal(s) are expected to close within 7 days.",
            ];
        }

        $inactiveContacts = User::where('is_admin', false)
            ->where(function ($q) {
                $q->where('last_engagement_at', '<', Carbon::now()->subMonths(6))
                  ->orWhereNull('last_engagement_at');
            })
            ->count();
        if ($inactiveContacts > 5) {
            $insights[] = [
                'type' => 'neutral',
                'icon' => 'fa-user-clock',
                'title' => 'Re-engagement Opportunity',
                'message' => "{$inactiveContacts} contacts have been inactive for 6+ months. Consider a re-engagement campaign.",
            ];
        }

        $wonThisMonth = Deal::whereHas('stage', function ($q) { $q->where('is_won', true); })
            ->whereMonth('actual_close_date', Carbon::now()->month)
            ->whereYear('actual_close_date', Carbon::now()->year)
            ->sum('value');
        if ($wonThisMonth > 0) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'fa-trophy',
                'title' => 'Monthly Wins',
                'message' => 'Closed ' . number_format($wonThisMonth, 2) . ' ZMW in deals this month.',
            ];
        }

        return $insights;
    }
}
