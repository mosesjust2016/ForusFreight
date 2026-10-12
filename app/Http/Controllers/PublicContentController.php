<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBaseArticle;
use App\Models\LandingPage;
use App\Models\LandingPageVisit;
use Illuminate\Support\Str;

class PublicContentController extends Controller
{
    /* ─────────── Knowledge Base (Public) ─────────── */

    public function knowledgeBaseHome(Request $request)
    {
        $query = KnowledgeBaseArticle::where('status', 'published')
            ->where('is_internal', false)
            ->orderByDesc('views');

        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $articles = $query->latest()->paginate(12);

        $categories = KnowledgeBaseArticle::where('status', 'published')
            ->where('is_internal', false)
            ->distinct()
            ->pluck('category');

        $popular = KnowledgeBaseArticle::where('status', 'published')
            ->where('is_internal', false)
            ->orderByDesc('views')
            ->take(5)
            ->get();

        return view('public.kb.index', compact('articles', 'categories', 'popular'));
    }

    public function knowledgeBaseArticle($slug)
    {
        $article = KnowledgeBaseArticle::where('slug', $slug)
            ->where('status', 'published')
            ->where('is_internal', false)
            ->firstOrFail();

        $article->increment('views');

        $related = KnowledgeBaseArticle::where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->where('status', 'published')
            ->where('is_internal', false)
            ->take(4)
            ->get();

        return view('public.kb.article', compact('article', 'related'));
    }

    /* ─────────── Landing Pages (Public) ─────────── */

    public function landingPage(Request $request, $slug)
    {
        $page = LandingPage::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $page->increment('views');

        // Capture UTM parameters if present
        $utm = [
            'source' => $request->get('utm_source', $page->campaign_source),
            'medium' => $request->get('utm_medium', $page->campaign_medium),
            'campaign' => $request->get('utm_campaign'),
            'content' => $request->get('utm_content'),
        ];

        $visit = LandingPageVisit::create([
            'landing_page_id' => $page->id,
            'source' => $utm['source'] ?: 'direct',
            'medium' => $utm['medium'] ?: 'direct',
            'campaign' => $utm['campaign'],
            'session_key' => Str::random(40),
        ]);

        return view('public.lp.page', compact('page', 'utm', 'visit'));
    }

    public function landingPageEngagement(Request $request, $slug)
    {
        $page = LandingPage::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $validated = $request->validate(['visit_id' => 'required|integer', 'duration_seconds' => 'required|integer|min:0|max:86400', 'bounced' => 'required|boolean', 'event' => 'nullable|in:cta_click']);
        $visitUpdate = [
            'duration_seconds' => $validated['duration_seconds'],
            'bounced' => $validated['bounced'],
        ];
        if (($validated['event'] ?? null) === 'cta_click') {
            $visitUpdate['cta_clicked'] = true;
            $visitUpdate['cta_type'] = $request->string('cta_type')->limit(40)->toString();
        }
        LandingPageVisit::where('id', $validated['visit_id'])->where('landing_page_id', $page->id)->update($visitUpdate);
        return response()->json(['ok' => true]);
    }

    public function landingPageSubmit(Request $request, $slug)
    {
        $page = LandingPage::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $fields = $page->form_fields ?: ['name', 'email', 'phone', 'message'];
        $rules = ['email' => 'required|email|max:255', 'consent' => 'required|accepted'];
        foreach (['name' => 'string|max:255', 'phone' => 'string|max:50', 'company' => 'string|max:255', 'service' => 'string|max:255', 'origin' => 'string|max:255', 'destination' => 'string|max:255', 'message' => 'string|max:5000'] as $field => $rule) {
            $rules[$field] = in_array($field, $fields, true) ? 'required|' . $rule : 'nullable|' . $rule;
        }
        $validated = $request->validate($rules);

        $page->increment('submissions');

        // Store submission (could be expanded to store in a leads table)
        \App\Models\User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'] ?? $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'is_admin' => false,
                'crm_status' => 'lead',
                'lead_source' => 'landing_page:' . $page->slug,
            ]
        );

        return redirect()->route('thank-you');
    }
}
