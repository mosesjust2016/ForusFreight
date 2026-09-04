<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Campaign;
use App\Models\LandingPage;

class CrmMarketingController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    public function campaigns(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $query = Campaign::query();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $campaigns = $query->latest()->paginate(20);
        $stats = [
            'active' => Campaign::where('status', 'active')->count(),
            'total_budget' => Campaign::sum('budget') ?? 0,
            'total_spent' => Campaign::sum('spent') ?? 0,
            'total_leads' => Campaign::sum('leads_generated'),
            'total_conversions' => Campaign::sum('conversions'),
        ];

        return view('admin.crm.marketing.campaigns', compact('campaigns', 'stats'));
    }

    public function storeCampaign(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'status' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'target_audience' => 'nullable|string',
        ]);
        Campaign::create($validated);
        return back()->with('success', 'Campaign created successfully.');
    }

    public function updateCampaign(Request $request, Campaign $campaign)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'budget' => 'nullable|numeric',
            'spent' => 'nullable|numeric',
            'leads_generated' => 'nullable|integer',
            'conversions' => 'nullable|integer',
        ]);
        $campaign->update($validated);
        return back()->with('success', 'Campaign updated.');
    }

    public function landingPages()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $pages = LandingPage::latest()->paginate(20);
        return view('admin.crm.marketing.landing_pages', compact('pages'));
    }

    public function storeLandingPage(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:landing_pages,slug',
            'content' => 'nullable|string',
            'campaign_source' => 'nullable|string',
            'campaign_medium' => 'nullable|string',
        ]);
        $validated['status'] = 'draft';
        LandingPage::create($validated);
        return back()->with('success', 'Landing page created.');
    }

    public function updateLandingPageStatus(Request $request, LandingPage $page)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $request->validate(['status' => 'required|in:draft,published,archived']);
        $page->update(['status' => $request->status]);
        return back()->with('success', 'Landing page status updated.');
    }
}
