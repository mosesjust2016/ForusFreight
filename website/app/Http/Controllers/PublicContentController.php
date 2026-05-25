<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KnowledgeBaseArticle;
use App\Models\LandingPage;

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

        return view('public.lp.page', compact('page', 'utm'));
    }

    public function landingPageSubmit(Request $request, $slug)
    {
        $page = LandingPage::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'nullable|string',
        ]);

        $page->increment('submissions');

        // Store submission (could be expanded to store in a leads table)
        \App\Models\User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'is_admin' => false,
                'crm_status' => 'lead',
                'lead_source' => 'landing_page:' . $page->slug,
            ]
        );

        return back()->with('success', 'Thank you! We will contact you shortly.');
    }
}
