<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Campaign;
use App\Models\LandingPage;
use Mews\Purifier\Facades\Purifier;

class CrmMarketingController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
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
        $totalLeads = (int) Campaign::sum('leads_generated');
        $totalConversions = (int) Campaign::sum('conversions');
        $stats = [
            'active' => Campaign::where('status', 'active')->count(),
            'total_budget' => Campaign::sum('budget') ?? 0,
            'total_spent' => Campaign::sum('spent') ?? 0,
            'total_leads' => $totalLeads,
            'total_conversions' => $totalConversions,
            'conversion_rate' => $totalLeads > 0 ? round(($totalConversions / $totalLeads) * 100, 1) : 0,
            'budget_used' => (float) Campaign::sum('budget') > 0 ? round((Campaign::sum('spent') / Campaign::sum('budget')) * 100, 1) : 0,
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
        $pages = LandingPage::latest()->paginate(10)->withQueryString();
        return view('admin.crm.marketing.landing_pages', compact('pages'));
    }

    public function createLandingPage()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        return view('admin.crm.marketing.landing_page_builder', ['page' => new LandingPage(['template' => 'freight_quote', 'show_form' => true, 'form_fields' => ['name', 'email', 'phone', 'message'], 'cta_label' => 'Get a quote', 'primary_color' => '#ff6200', 'accent_color' => '#007f7f'])]);
    }

    public function editLandingPage(LandingPage $page)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        return view('admin.crm.marketing.landing_page_builder', compact('page'));
    }

    public function previewLandingPage(LandingPage $page)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        return view('public.lp.page', ['page' => $page, 'utm' => []]);
    }

    public function destroyLandingPage(LandingPage $page)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        if ($page->hero_image_url) \Illuminate\Support\Facades\Storage::disk('public')->delete($page->hero_image_url);
        foreach ($page->gallery_images ?: [] as $image) \Illuminate\Support\Facades\Storage::disk('public')->delete($image);
        $page->delete();
        return redirect()->route('admin.crm.landing-pages')->with('success', 'Landing page deleted.');
    }

    public function storeLandingPage(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $this->validateLandingPage($request);
        $validated['show_form'] = $request->boolean('show_form');
        $validated['form_fields'] = $this->cleanFormFields($request->input('form_fields', []));
        $validated['gallery_images'] = $this->storeGalleryImages($request);
        if ($request->hasFile('hero_image')) $validated['hero_image_url'] = $request->file('hero_image')->store('landing-pages', 'public');
        $validated['content'] = Purifier::clean($validated['content'] ?? '');
        $validated['status'] = 'draft';
        LandingPage::create($validated);
        return redirect()->route('admin.crm.landing-pages')->with('success', 'Landing page created as a draft.');
    }

    public function updateLandingPage(Request $request, LandingPage $page)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $validated = $this->validateLandingPage($request, $page);
        $validated['show_form'] = $request->boolean('show_form');
        $validated['form_fields'] = $this->cleanFormFields($request->input('form_fields', []));
        $validated['gallery_images'] = array_merge($page->gallery_images ?: [], $this->storeGalleryImages($request));
        if ($request->hasFile('hero_image')) {
            if ($page->hero_image_url) Storage::disk('public')->delete($page->hero_image_url);
            $validated['hero_image_url'] = $request->file('hero_image')->store('landing-pages', 'public');
        }
        $validated['content'] = Purifier::clean($validated['content'] ?? '');
        $page->update($validated);
        return redirect()->route('admin.crm.landing-pages')->with('success', 'Landing page updated.');
    }

    private function validateLandingPage(Request $request, ?LandingPage $page = null): array
    {
        $slugRule = 'required|string|max:120|alpha_dash|unique:landing_pages,slug' . ($page ? ',' . $page->id : '');
        return $request->validate([
            'title' => 'required|string|max:255', 'slug' => $slugRule, 'template' => 'required|in:freight_quote,lead_generation,corporate_logistics,service_promo,tracking_lead,home_page,services_page', 'content' => 'nullable|string',
            'hero_title' => 'nullable|string|max:255', 'hero_text' => 'nullable|string|max:1000',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 'cta_label' => 'required|string|max:80',
            'cta_url' => 'nullable|string|max:1000', 'form_title' => 'nullable|string|max:255',
            'form_description' => 'nullable|string|max:1000', 'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'campaign_source' => 'nullable|string|max:100', 'campaign_medium' => 'nullable|string|max:100',
        ]);
    }

    private function cleanFormFields(array $fields): array
    {
        $allowed = ['name', 'email', 'phone', 'company', 'service', 'origin', 'destination', 'message'];
        $fields = array_values(array_unique(array_intersect($allowed, $fields)));
        return in_array('email', $fields, true) ? $fields : array_merge(['email'], $fields);
    }

    private function storeGalleryImages(Request $request): array
    {
        return collect($request->file('gallery_images', []))->filter()->map(fn ($image) => $image->store('landing-pages/gallery', 'public'))->values()->all();
    }

    public function updateLandingPageStatus(Request $request, LandingPage $page)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        $request->validate(['status' => 'required|in:draft,published,archived']);
        $page->update(['status' => $request->status]);
        return back()->with('success', 'Landing page status updated.');
    }
}
