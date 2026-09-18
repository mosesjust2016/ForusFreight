@php
$page = \App\Models\CmsPage::where('slug', 'refund-policy')->first();
$sections = $page?->sections ?? [];
@endphp
@extends('layouts.document')

@section('title', ($page?->title ?? 'Refund Policy') . ' - Forus Freight')
@section('meta_description', 'Forus Freight\'s refund and cancellation policy, consistent with the Competition and Consumer Protection Act of Zambia.')

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'Refund Policy', 'url' => null]]])

<!-- HERO -->
<section style="padding: 5rem 0; background: linear-gradient(135deg, rgb(0,127,127), #004c4c);">
    <div class="container">
        <div style="max-width: 760px; margin: auto; text-align: center; color: #fff;">
            <div style="display:inline-flex; align-items:center; gap:.5rem; background:rgba(255,255,255,.15); padding:.4rem 1rem; border-radius:50px; font-size:.8rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:1.5rem;">
                <i class="fas fa-receipt"></i> Legal
            </div>
            <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 1rem; line-height:1.15;">{{ $sections['title'] ?? 'Refund Policy' }}</h1>
            <p style="opacity:.85; font-size:1.05rem;">
                {{ $sections['subtitle'] ?? 'Your rights to a refund, replacement, or re-performance of service.' }}
            </p>
            <p style="opacity:.65; font-size:.85rem; margin-top:1rem;">Last updated: {{ date('F j, Y') }}</p>
        </div>
    </div>
</section>

<!-- CONTENT -->
<section style="padding: 5rem 0; background: #f8fafc;">
    <div class="container">
        <div style="max-width: 900px; margin: auto;">
            <article style="background: #fff; border-radius: 24px; padding: 3.5rem; box-shadow: 0 4px 24px rgba(0,0,0,.07); border: 1px solid #e2e8f0; color: #334155; line-height: 1.8; font-size: .97rem;">

                {!! $sections['content'] ?? '<p style="color:#64748b; margin-bottom:2.5rem;">
                    This Refund Policy explains when <strong>Forus Freight Limited</strong> will refund, replace, or re-perform a service, consistent with the <strong>Competition and Consumer Protection Act, 2010</strong> of Zambia. It should be read together with our <a href="'.route('terms').'" style="color:rgb(0,127,127); font-weight:700;">Terms &amp; Conditions</a>.
                </p>' !!}

                <div style="margin-bottom:2.5rem;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">Cancelling a Booking</h2>
                    <p>If you cancel a booked shipment before collection or pickup has occurred, we will refund any amount you paid in excess of costs Forus Freight has already reasonably incurred on your behalf (for example, booking or administrative fees already committed).</p>
                </div>

                <div style="margin-bottom:2.5rem;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">If a Service Isn't Performed Properly</h2>
                    <p>If Forus Freight fails to perform a service with reasonable care and skill, or within a reasonable time, you are entitled to have the service re-performed to a reasonable standard, or to a refund of the amount paid for that specific service. This right exists regardless of anything else stated on our website or in any invoice, and cannot be waived.</p>
                </div>

                <div style="margin-bottom:2.5rem;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">How Refunds Are Processed</h2>
                    <p>Approved refunds are processed within 14 days of the refund being agreed, using the original payment method where practicable.</p>
                </div>

                <div style="margin-bottom:2.5rem;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">What This Policy Does Not Do</h2>
                    <p>This policy does not limit, exclude, or purport to waive any right or remedy you have under Zambian law. Nothing on this website or in our Terms should be read as a "no refund" notice — Zambian consumer law does not permit a business to disclaim a consumer's statutory rights in this way.</p>
                </div>

                <div style="background:#f0fafa; border-radius:16px; padding:2rem; border:1px solid #b2d8d8;">
                    <h2 style="font-size:1.3rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">Request a Refund</h2>
                    <p>Contact us at <strong>info@forusfl.co.zm</strong> or <strong>+260 572 788 685</strong> with your booking or waybill reference, and a description of the issue.</p>
                </div>

            </article>
        </div>
    </div>
</section>

@endsection
