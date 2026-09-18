@php
$page = \App\Models\CmsPage::where('slug', 'cookie-policy')->first();
$sections = $page?->sections ?? [];
@endphp
@extends('layouts.app')

@section('title', ($page?->title ?? 'Cookie Policy') . ' - Forus Freight')
@section('meta_description', 'What cookies and similar technologies Forus Freight\'s website uses, and why.')

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'Cookie Policy', 'url' => null]]])

<!-- HERO -->
<section style="padding: 5rem 0; background: linear-gradient(135deg, rgb(0,127,127), #004c4c);">
    <div class="container">
        <div style="max-width: 760px; margin: auto; text-align: center; color: #fff;">
            <div style="display:inline-flex; align-items:center; gap:.5rem; background:rgba(255,255,255,.15); padding:.4rem 1rem; border-radius:50px; font-size:.8rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:1.5rem;">
                <i class="fas fa-cookie-bite"></i> Legal
            </div>
            <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 1rem; line-height:1.15;">{{ $sections['title'] ?? 'Cookie Policy' }}</h1>
            <p style="opacity:.85; font-size:1.05rem;">
                {{ $sections['subtitle'] ?? 'What cookies and similar technologies this website uses, and why.' }}
            </p>
            <p style="opacity:.65; font-size:.85rem; margin-top:1rem;">Last updated: {{ date('F j, Y') }}</p>
        </div>
    </div>
</section>

<!-- CONTENT -->
<section style="padding: 5rem 0; background: #f8fafc;">
    <div class="container">
        <div style="max-width: 760px; margin: auto;">
            <article style="background: #fff; border-radius: 24px; padding: 3rem; box-shadow: 0 4px 24px rgba(0,0,0,.07); border: 1px solid #e2e8f0; color: #334155; line-height: 1.8; font-size: .97rem;">

                {!! $sections['content'] ?? '<p style="color:#64748b; margin-bottom:2.5rem;">
                    This Cookie Policy explains what cookies and similar technologies <strong>Forus Freight Limited</strong> uses on this website, and why. It should be read together with our <a href="'.route('privacy').'" style="color:rgb(0,127,127); font-weight:700;">Privacy Policy</a>.
                </p>' !!}

                <div style="margin-bottom:2.5rem;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">What Are Cookies?</h2>
                    <p>Cookies are small text files placed on your device by a website you visit. They are widely used to make websites work, work more efficiently, and to provide information to the site owner.</p>
                </div>

                <div style="margin-bottom:2.5rem;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">Strictly Necessary Cookies We Use</h2>
                    <p style="margin-bottom:1rem;">We currently use only the cookies required for the website and client portal to function. We do <strong>not</strong> use any advertising or behavioural-tracking cookies, and we do not run Google Analytics, Facebook Pixel, or any similar analytics service on this site.</p>
                    <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:.9rem;">
                        <thead>
                            <tr style="text-align:left; border-bottom:2px solid #e2e8f0;">
                                <th style="padding:.6rem .5rem;">Cookie</th>
                                <th style="padding:.6rem .5rem;">Purpose</th>
                                <th style="padding:.6rem .5rem;">Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:.6rem .5rem;"><code>forus_freight_session</code></td>
                                <td style="padding:.6rem .5rem;">Keeps you logged in and remembers your session while you browse or use the client portal.</td>
                                <td style="padding:.6rem .5rem;">Session / until logout</td>
                            </tr>
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:.6rem .5rem;"><code>XSRF-TOKEN</code></td>
                                <td style="padding:.6rem .5rem;">Security token that protects forms on this site from cross-site request forgery attacks.</td>
                                <td style="padding:.6rem .5rem;">Session</td>
                            </tr>
                            <tr>
                                <td style="padding:.6rem .5rem;"><code>cookie_consent</code></td>
                                <td style="padding:.6rem .5rem;">Remembers that you have seen and dismissed our cookie notice, so it isn't shown again.</td>
                                <td style="padding:.6rem .5rem;">12 months</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <p style="margin-top:1rem;">Because these cookies are strictly necessary, they cannot be switched off from within our site, but you can block or delete them at any time using your browser's settings. Doing so may prevent you from logging in or using the client portal.</p>
                </div>

                <div style="margin-bottom:2.5rem;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">Third-Party Resources</h2>
                    <p>Some pages load fonts, icons, or a visual element from third-party content delivery networks (Google Fonts, cdnjs, and unpkg). Loading these resources means your browser makes a direct request to that provider, which may see your IP address and browser details as part of a normal web request — this is standard for any site using external fonts or icon libraries. These providers are not used by us for advertising or analytics, and to our knowledge do not use this to set tracking cookies for our site specifically. If you would prefer not to make these requests, you can block third-party content in your browser, though some pages may not display correctly.</p>
                </div>

                <div style="margin-bottom:2.5rem;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">Managing Cookies in Your Browser</h2>
                    <p>Most browsers let you view, delete, and block cookies through their settings menu (usually under "Privacy" or "Security"). Refer to your browser's help documentation for exact steps, as they vary by browser and version.</p>
                </div>

                <div style="margin-bottom:2.5rem;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">Changes to This Policy</h2>
                    <p>If we add analytics, advertising, or other non-essential cookies in the future, we will update this page and, where required, ask for your consent before those cookies are set.</p>
                </div>

                <div style="background:#f0fafa; border-radius:16px; padding:2rem; border:1px solid #b2d8d8;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">Questions?</h2>
                    <p>Contact us at <strong>info@forusfl.co.zm</strong> or <strong>+260 572 788 685</strong>.</p>
                </div>

            </article>
        </div>
    </div>
</section>

@endsection
