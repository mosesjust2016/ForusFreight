@php
$page = \App\Models\CmsPage::where('slug', 'contact')->first();
$sections = $page?->sections ?? [];
@endphp
@extends('layouts.app')

@section('title', ($page?->title ?? 'Contact Us') . ' - Forus Freight')

@section('content')

<!-- HERO -->
<section style="padding: 5rem 0; background: linear-gradient(135deg, rgb(0,127,127), #004c4c);">
    <div class="container">
        <div style="max-width: 760px; margin: auto; text-align: center; color: rgb(255,255,255);">
            <h1 style="font-size: 3.2rem; font-weight: 900; margin-bottom: 1rem;">
                {{ $sections['title'] ?? 'Contact Forus Freight' }}
            </h1>
            <p style="opacity: .9; font-size: 1.1rem;">
                {{ $sections['subtitle'] ?? 'We\'re here to move your cargo — safely, quickly and professionally.' }}
            </p>
        </div>
    </div>
</section>

<!-- CONTACT SECTION -->
<section style="padding: 6rem 0; background: rgb(204,204,204);">
    <div class="container">

        <div style="
            max-width: 1100px;
            margin: auto;
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 3rem;
        ">

            <!-- LEFT FORM -->
            <div style="
                background: rgb(255,255,255);
                border-radius: 28px;
                padding: 3rem;
                box-shadow: 0 25px 60px rgba(0,0,0,.15);
            ">

                <h2 style="font-size: 2.1rem; font-weight: 900; color: rgb(0,127,127); margin-bottom: .5rem;">
                    Send Us a Message
                </h2>
                <p style="color: #444; margin-bottom: 2.5rem;">
                    Our team responds within 24 hours.
                </p>

                <form method="POST" action="#">
                    @csrf

                    <div style="display: grid; grid-template-columns: repeat(2,1fr); gap: 1.5rem; margin-bottom:1.5rem;">
                        <input type="text" placeholder="Full Name" required
                               style="width:100%; padding:1rem; border-radius:12px; border:2px solid rgb(204,204,204);">
                        <input type="email" placeholder="Email Address" required
                               style="width:100%; padding:1rem; border-radius:12px; border:2px solid rgb(204,204,204);">
                    </div>

                    <div style="margin-bottom:1.5rem;">
                        <input type="text" placeholder="Phone Number" required
                               style="width:100%; padding:1rem; border-radius:12px; border:2px solid rgb(204,204,204);">
                    </div>

                    <div style="margin-bottom:2rem;">
                        <textarea rows="5" placeholder="Your Message" required
                            style="width:100%; padding:1rem; border-radius:12px; border:2px solid rgb(204,204,204);"></textarea>
                    </div>

                    <button type="submit" style="
                        width: 100%;
                        padding: 1.2rem;
                        border-radius: 14px;
                        font-weight: 800;
                        color: rgb(255,255,255);
                        background: linear-gradient(135deg, rgb(255,98,0), #ff8c42);
                        border: none;
                        cursor: pointer;
                        transition: .3s ease;
                    " onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                        Send Message
                    </button>

                </form>
            </div>

            <!-- RIGHT INFO -->
            <div style="
                background: linear-gradient(180deg, rgb(0,127,127), #003b3b);
                border-radius: 28px;
                padding: 3rem;
                color: rgb(255,255,255);
                box-shadow: 0 25px 60px rgba(0,0,0,.2);
            ">

                <h3 style="font-size: 2rem; font-weight: 900; margin-bottom: 2rem;">
                    Our Offices
                </h3>

                <div style="margin-bottom:2rem;">
                    <h4 style="font-weight:800; margin-bottom:.3rem; color: rgb(255,98,0);">Address</h4>
                    <p style="opacity:.9">{{ $sections['address'] ?? 'Lusaka, Zambia' }}</p>
                </div>

                <div style="margin-bottom:2rem;">
                    <h4 style="font-weight:800; margin-bottom:.3rem; color: rgb(255,98,0);">Phone</h4>
                    <p style="opacity:.9">{{ $sections['phone'] ?? '+260 97 123 4567' }}</p>
                </div>

                <div style="margin-bottom:2rem;">
                    <h4 style="font-weight:800; margin-bottom:.3rem; color: rgb(255,98,0);">Email</h4>
                    <p style="opacity:.9">{{ $sections['email'] ?? 'info@forusfl.co.zm' }}</p>
                </div>

                <div style="margin-bottom:2rem;">
                    <h4 style="font-weight:800; margin-bottom:.3rem; color: rgb(255,98,0);">Operating Hours</h4>
                    <p style="opacity:.9">{{ $sections['hours'] ?? 'Mon-Fri: 8:00 AM - 5:00 PM' }}</p>
                </div>

                <!-- CTA -->
                <div style="
                    margin-top: 3rem;
                    padding: 2rem;
                    border-radius: 18px;
                    background: rgba(255,255,255,.12);
                    text-align: center;
                ">
                    <p style="font-size:1.2rem; font-weight:800; margin-bottom:1rem;">
                        Need a fast quote?
                    </p>
                    <a href="{{ route('quote') }}" style="
                        display:inline-block;
                        padding: .9rem 2rem;
                        border-radius: 30px;
                        font-weight: 800;
                        color: rgb(255,255,255);
                        background: linear-gradient(135deg, rgb(255,98,0), #ff8c42);
                        text-decoration: none;
                    ">
                        Request a Quote
                    </a>
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
