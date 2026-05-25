<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CmsPage;
use Illuminate\Support\Str;

class CmsAdminController extends Controller
{
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $pages = CmsPage::latest()->get();
        return view('admin.cms.pages.index', compact('pages'));
    }

    public function create()
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        return view('admin.cms.pages.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'slug' => 'required|string|max:100|unique:cms_pages,slug',
            'title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['sections'] = $this->defaultSections($validated['slug']);
        $validated['last_edited_by'] = Auth::id();

        CmsPage::create($validated);
        return redirect()->route('admin.cms.pages.index')->with('success', 'Page created successfully.');
    }

    public function edit(CmsPage $page)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;
        return view('admin.cms.pages.edit', compact('page'));
    }

    public function update(Request $request, CmsPage $page)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:published,draft',
        ]);

        // Update sections from the form
        $sections = [];
        if ($request->has('sections')) {
            foreach ($request->input('sections', []) as $key => $section) {
                if (is_array($section)) {
                    $sections[$key] = array_filter($section, fn($v) => !is_null($v));
                }
            }
        }

        $validated['sections'] = $sections;
        $validated['last_edited_by'] = Auth::id();

        $page->update($validated);
        return back()->with('success', 'Page updated successfully.');
    }

    public function destroy(CmsPage $page)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        if (in_array($page->slug, ['home', 'about', 'services', 'contact', 'terms', 'footer'])) {
            return back()->with('error', 'Cannot delete core pages.');
        }

        $page->delete();
        return redirect()->route('admin.cms.pages.index')->with('success', 'Page deleted.');
    }

    public function uploadImage(Request $request)
    {
        if ($redirect = $this->checkAdmin()) return $redirect;

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $file = $validated['image'];
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/cms', $filename);

        return response()->json([
            'url' => asset('storage/cms/' . $filename),
            'filename' => $filename,
        ]);
    }

    private function defaultSections(string $slug): array
    {
        $defaults = [
            'home' => [
                'hero_title' => 'Forus Freight',
                'hero_subtitle' => 'Cross-border logistics made simple',
                'hero_cta_text' => 'Get a Quote',
                'hero_cta_link' => '/quote',
                'hero_image' => '',
                'features' => [
                    ['icon' => 'fa-truck', 'title' => 'Fast Shipping', 'text' => 'Reliable cross-border transport'],
                    ['icon' => 'fa-shield-halved', 'title' => 'Secure Cargo', 'text' => 'Full insurance coverage'],
                    ['icon' => 'fa-globe', 'title' => 'Zambia & SA', 'text' => 'Expert regional logistics'],
                ],
                'stats' => [
                    ['number' => '500+', 'label' => 'Monthly Shipments'],
                    ['number' => '98%', 'label' => 'On-Time Delivery'],
                    ['number' => '24/7', 'label' => 'Support Available'],
                ],
                'about_title' => 'About Forus Freight',
                'about_text' => 'We specialize in cross-border freight between Zambia and South Africa.',
                'about_image' => '',
            ],
            'about' => [
                'title' => 'About Us',
                'subtitle' => 'Your trusted logistics partner since 2015',
                'content' => 'Forus Freight was founded with a mission to simplify cross-border logistics between Zambia and South Africa.',
                'mission' => 'To deliver reliable, efficient, and cost-effective logistics solutions.',
                'vision' => 'To become the leading cross-border logistics provider in Southern Africa.',
                'team_title' => 'Our Leadership Team',
                'team' => [
                    ['name' => 'CEO', 'role' => 'Chief Executive Officer', 'image' => ''],
                    ['name' => 'COO', 'role' => 'Chief Operations Officer', 'image' => ''],
                ],
            ],
            'services' => [
                'title' => 'Our Services',
                'subtitle' => 'Comprehensive logistics solutions for your business',
                'services' => [
                    ['icon' => 'fa-truck', 'title' => 'Road Freight', 'description' => 'Full and part load road transport between Zambia and South Africa.'],
                    ['icon' => 'fa-warehouse', 'title' => 'Warehousing', 'description' => 'Secure storage facilities with inventory management.'],
                    ['icon' => 'fa-file-shield', 'title' => 'Customs Clearance', 'description' => 'Expert handling of all customs documentation and procedures.'],
                    ['icon' => 'fa-boxes-packing', 'title' => 'Packaging', 'description' => 'Professional packing and crating services for fragile cargo.'],
                    ['icon' => 'fa-headset', 'title' => '24/7 Support', 'description' => 'Round-the-clock customer service and shipment tracking.'],
                    ['icon' => 'fa-chart-line', 'title' => 'Supply Chain Consulting', 'description' => 'Optimize your logistics with our expert consultants.'],
                ],
            ],
            'contact' => [
                'title' => 'Contact Us',
                'subtitle' => 'We would love to hear from you',
                'address' => '123 Main Street, Lusaka, Zambia',
                'phone' => '+260 97 123 4567',
                'email' => 'info@forusfl.co.zm',
                'hours' => 'Mon-Fri: 8:00 AM - 5:00 PM',
                'map_embed' => '',
                'form_title' => 'Send us a Message',
            ],
            'terms' => [
                'title' => 'Terms of Service',
                'subtitle' => 'Last updated: January 2025',
                'content' => '<h2>1. Acceptance of Terms</h2><p>By accessing and using Forus Freight services, you agree to these terms.</p><h2>2. Services</h2><p>Forus Freight provides cross-border logistics and freight services.</p><h2>3. Liability</h2><p>We maintain full insurance coverage for all shipments.</p>',
            ],
            'footer' => [
                'description' => 'Fast, reliable & affordable logistics solutions across Zambia and the SADC region.',
                'social_facebook' => '#',
                'social_twitter' => '#',
                'social_linkedin' => '#',
                'social_instagram' => '#',
                'services_links' => [
                    ['title' => 'Same-Day Delivery', 'url' => '/services#same-day'],
                    ['title' => 'Cross-Border Shipping', 'url' => '/services#cross-border'],
                    ['title' => 'Warehousing', 'url' => '/services#warehousing'],
                    ['title' => 'Bulk Cargo', 'url' => '/services#bulk-cargo'],
                ],
                'company_links' => [
                    ['title' => 'About Us', 'url' => '/about'],
                    ['title' => 'Services', 'url' => '/services'],
                    ['title' => 'Get Quote', 'url' => '/quote'],
                    ['title' => 'Track Shipment', 'url' => '/tracking'],
                ],
                'contact_phones' => [
                    ['number' => '+260 572 7886857', 'label' => ''],
                    ['number' => '+260 766 193059', 'label' => ''],
                ],
                'contact_support_label' => '24/7 Support',
                'contact_email' => 'info@forusfl.co.zm',
                'contact_email_label' => 'Email Us',
                'contact_address' => 'Forus Freight Ltd METROLUX PLAZA Plot No. 401A/8 Kafure Road',
                'contact_city' => 'Lusaka, Zambia',
                'copyright_text' => '© {year} Forus Freight. All rights reserved.',
                'whatsapp_number' => '260961234567',
                'whatsapp_message' => 'Hi Forus Freight, I need a quote for logistics services',
            ],
        ];

        return $defaults[$slug] ?? ['title' => 'New Page', 'content' => ''];
    }
}
