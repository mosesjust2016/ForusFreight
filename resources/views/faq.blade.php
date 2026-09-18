@php
$page = \App\Models\CmsPage::where('slug', 'faq')->first();
$sections = $page?->sections ?? [];
$faqs = [
    ['q' => 'How long does shipping typically take?', 'a' => 'Shipping times depend on the destination and service type. Road freight typically takes 2-7 days within SADC countries. Air freight is available for urgent shipments (1-3 days). We\'ll provide a specific timeframe when you request a quote.'],
    ['q' => 'Can you handle international shipments?', 'a' => 'Yes. We specialize in international logistics across Africa and beyond. Our services include customs clearance, documentation, and coordination with international partners to ensure smooth delivery.'],
    ['q' => 'What insurance options are available?', 'a' => 'We offer comprehensive insurance coverage for all shipments. Standard coverage is included, with options for additional coverage based on cargo value. Full details are available in your shipment quote.'],
    ['q' => 'How do I track my shipment?', 'a' => 'You can track your shipment through our online portal. Log in with your account or use the tracking number provided. Real-time updates are sent via SMS and email throughout the delivery process.'],
    ['q' => 'What payment methods do you accept?', 'a' => 'We accept bank transfer and mobile money. Contact us for full payment details for your shipment.'],
    ['q' => 'Do you offer warehousing services?', 'a' => 'Yes, we operate warehousing facilities. Services include storage, inventory management, order fulfillment, and cross-docking. Contact us for facility details and rates.'],
    ['q' => 'What are your business hours?', 'a' => 'Our offices are open Monday to Friday, 08:00-17:00 CAT. You can reach us or submit an enquiry outside these hours and we will respond the next business day.'],
    ['q' => 'How do I request a refund or raise a complaint?', 'a' => 'Contact us at info@forusfl.co.zm or +260 572 788 685 with your booking or waybill reference. See our Refund Policy for full details on when a refund or re-performance applies.'],
];
@endphp
@extends('layouts.document')

@section('title', ($page?->title ?? 'Frequently Asked Questions') . ' - Forus Freight')
@section('meta_description', 'Answers to common questions about Forus Freight\'s shipping times, tracking, insurance, payment methods, and warehousing services.')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $i => $item)
        {
            "@type": "Question",
            "name": {!! json_encode($item['q']) !!},
            "acceptedAnswer": { "@type": "Answer", "text": {!! json_encode($item['a']) !!} }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endsection

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'FAQ', 'url' => null]]])

<!-- HERO -->
<section style="padding: 5rem 0; background: linear-gradient(135deg, rgb(0,127,127), #004c4c);">
    <div class="container">
        <div style="max-width: 760px; margin: auto; text-align: center; color: #fff;">
            <div style="display:inline-flex; align-items:center; gap:.5rem; background:rgba(255,255,255,.15); padding:.4rem 1rem; border-radius:50px; font-size:.8rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:1.5rem;">
                <i class="fas fa-circle-question"></i> Support
            </div>
            <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 1rem; line-height:1.15;">{{ $sections['title'] ?? 'Frequently Asked Questions' }}</h1>
            <p style="opacity:.85; font-size:1.05rem;">
                {{ $sections['subtitle'] ?? 'Answers to common questions about our services.' }}
            </p>
        </div>
    </div>
</section>

<!-- CONTENT -->
<section style="padding: 5rem 0; background: #f8fafc;">
    <div class="container">
        <div style="max-width: 800px; margin: auto; display: flex; flex-direction: column; gap: 1rem;">
            @foreach($faqs as $item)
            <div style="background: #fff; border-radius: 16px; padding: 1.75rem 2rem; box-shadow: 0 4px 24px rgba(0,0,0,.06); border: 1px solid #e2e8f0;">
                <h2 style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-bottom: .6rem;">{{ $item['q'] }}</h2>
                <p style="color: #475569; line-height: 1.7; margin: 0;">{{ $item['a'] }}</p>
            </div>
            @endforeach

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: #64748b; margin-bottom: 1rem;">Still have a question?</p>
                <a href="{{ route('contact') }}" style="display: inline-block; background: rgb(0,127,127); color: #fff; padding: .9rem 2rem; border-radius: 12px; font-weight: 700; text-decoration: none;">Contact Us</a>
            </div>
        </div>
    </div>
</section>

@endsection
