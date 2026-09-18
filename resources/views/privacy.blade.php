@php
$page = \App\Models\CmsPage::where('slug', 'privacy')->first();
$sections = $page?->sections ?? [];
@endphp
@extends('layouts.app')

@section('title', ($page?->title ?? 'Privacy Policy') . ' - Forus Freight')
@section('meta_description', 'How Forus Freight Limited collects, uses, and protects your personal data, in accordance with the Data Protection Act No. 3 of 2021 of Zambia.')

@section('content')
@include('partials.breadcrumbs', ['crumbs' => [['label' => 'Privacy Policy', 'url' => null]]])

<!-- HERO -->
<section style="padding: 5rem 0; background: linear-gradient(135deg, rgb(0,127,127), #004c4c);">
    <div class="container">
        <div style="max-width: 760px; margin: auto; text-align: center; color: #fff;">
            <div style="display:inline-flex; align-items:center; gap:.5rem; background:rgba(255,255,255,.15); padding:.4rem 1rem; border-radius:50px; font-size:.8rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:1.5rem;">
                <i class="fas fa-shield-halved"></i> Legal
            </div>
            <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 1rem; line-height:1.15;">{{ $sections['title'] ?? 'Privacy Policy' }}</h1>
            <p style="opacity:.85; font-size:1.05rem;">
                {{ $sections['subtitle'] ?? 'How Forus Freight Limited collects, uses, and protects your personal data.' }}
            </p>
            <p style="opacity:.65; font-size:.85rem; margin-top:1rem;">Last updated: {{ date('F j, Y') }}</p>
        </div>
    </div>
</section>

<!-- CONTENT -->
<section style="padding: 5rem 0; background: #f8fafc;">
    <div class="container">
        <div style="max-width: 900px; margin: auto; display: grid; grid-template-columns: 260px 1fr; gap: 3rem; align-items: start;">

            <nav aria-label="Table of contents" style="position: sticky; top: 90px; background: #fff; border-radius: 20px; padding: 1.75rem; box-shadow: 0 4px 24px rgba(0,0,0,.07); border: 1px solid #e2e8f0;">
                <p style="font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#475569; margin-bottom:1rem;">Contents</p>
                <div style="display:flex; flex-direction:column; gap:.35rem;">
                    @foreach([
                        ['#who-we-are',      'Who We Are'],
                        ['#data-we-collect', 'Data We Collect'],
                        ['#why-we-collect',  'Why We Collect It'],
                        ['#sharing',         'Who We Share It With'],
                        ['#retention',       'How Long We Keep It'],
                        ['#your-rights',     'Your Rights'],
                        ['#cookies',         'Cookies'],
                        ['#security',        'Security'],
                        ['#children',        'Children'],
                        ['#changes',         'Changes to This Policy'],
                        ['#contact',         'Contact & Complaints'],
                    ] as [$href, $label])
                    <a href="{{ $href }}"
                       style="font-size:.85rem; color:#475569; text-decoration:none; padding:.3rem .6rem; border-radius:8px; transition:.2s;"
                       onmouseover="this.style.background='rgb(0,127,127)'; this.style.color='#fff';"
                       onmouseout="this.style.background='transparent'; this.style.color='#475569';">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
            </nav>

            <article style="background: #fff; border-radius: 24px; padding: 3rem; box-shadow: 0 4px 24px rgba(0,0,0,.07); border: 1px solid #e2e8f0; color: #334155; line-height: 1.8; font-size: .97rem;">

                {!! $sections['content'] ?? '<p style="color:#64748b; margin-bottom:2.5rem;">
                    This Privacy Policy explains how <strong>Forus Freight Limited</strong> ("Forus Freight", "we", "us", or "our") collects, uses, discloses, and protects personal data when you visit our website, request a quote, book a shipment, or otherwise interact with us. It should be read together with our <a href="'.route('terms').'" style="color:rgb(0,127,127); font-weight:700;">Terms &amp; Conditions</a> and <a href="'.route('cookie-policy').'" style="color:rgb(0,127,127); font-weight:700;">Cookie Policy</a>. This Policy is written to comply with the Data Protection Act No. 3 of 2021 of Zambia.
                </p>' !!}

                <div id="who-we-are" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">01</span> Who We Are
                    </h2>
                    <p>Forus Freight Limited is a logistics and freight company registered in Zambia (PACRA Registration No. 120251030444, TPIN 2003929264), with registered address at METROLUX PLAZA, Plot No. 401A/8 Kafure Road, Lusaka, Zambia. We are the "data controller" responsible for the personal data described in this Policy.</p>
                </div>

                <div id="data-we-collect" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">02</span> Data We Collect
                    </h2>
                    <p style="margin-bottom:.8rem;">We only collect personal data that is reasonably necessary for the purpose it is collected for. Depending on how you interact with us, this may include:</p>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li><strong>Contact details</strong> — name, email address, phone number, and company name, when you submit a quote request, contact form, or booking.</li>
                        <li><strong>Shipment details</strong> — origin, destination, cargo description, weight, dimensions, and value, needed to move and track your consignment.</li>
                        <li><strong>Account credentials</strong> — email/phone and a securely hashed password, if you register for a client account.</li>
                        <li><strong>Communications</strong> — messages, enquiries, or support tickets you send us, and records of SMS/WhatsApp/email correspondence about your shipment.</li>
                        <li><strong>Payment-related information</strong> — invoice and payment status for services rendered. We do not process or store card numbers on our servers.</li>
                        <li><strong>Technical data</strong> — standard web server logs (IP address, browser type, pages visited) generated automatically when you use our website.</li>
                    </ul>
                    <p style="margin-top:.8rem;">We do not ask for, and you should not send us, sensitive personal data (health, biometric, religious, or political information) unless it is unavoidably part of a shipment description (e.g. medical supplies).</p>
                </div>

                <div id="why-we-collect" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">03</span> Why We Collect It
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>To provide quotes, process bookings, and deliver freight, customs, and warehousing services (performance of a contract).</li>
                        <li>To communicate with you about your shipment's status, invoices, or support requests.</li>
                        <li>To comply with customs, tax, and other legal or regulatory obligations.</li>
                        <li>Where you have given consent — for example, marketing communications or optional cookies — and which you may withdraw at any time.</li>
                    </ul>
                </div>

                <div id="sharing" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">04</span> Who We Share It With
                    </h2>
                    <p style="margin-bottom:.8rem;">We do not sell your personal data. We share it only where necessary with:</p>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Sub-contracted carriers and clearing agents, to the extent needed to move your consignment.</li>
                        <li>Customs authorities, the Zambia Revenue Authority, and other regulators, where legally required.</li>
                        <li>Service providers who send transactional email, SMS, and WhatsApp messages on our behalf (for booking confirmations, OTPs, and shipment updates) — these providers process data only under our instructions and only for that purpose.</li>
                        <li>Professional advisers (auditors, lawyers) where necessary, and law enforcement where required by law.</li>
                    </ul>
                </div>

                <div id="retention" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">05</span> How Long We Keep It
                    </h2>
                    <p>We retain shipment and account records for as long as your account is active and for a reasonable period afterward to meet tax, customs, and legal record-keeping obligations, and to resolve any disputes. Contact-form and quote enquiries that do not result in a booking are retained only as long as needed to respond to you, and are periodically deleted thereafter.</p>
                </div>

                <div id="your-rights" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">06</span> Your Rights
                    </h2>
                    <p style="margin-bottom:.8rem;">Under the Data Protection Act No. 3 of 2021, you have the right to:</p>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Be informed about how your personal data is processed;</li>
                        <li>Access a copy of the personal data we hold about you;</li>
                        <li>Request correction of inaccurate or incomplete data;</li>
                        <li>Request deletion of your data, where we are not legally required to keep it;</li>
                        <li>Object to or request restriction of certain processing; and</li>
                        <li>Withdraw consent at any time, without affecting processing carried out before withdrawal.</li>
                    </ul>
                    <p style="margin-top:.8rem;">To exercise any of these rights, email <strong>info@forusfl.co.zm</strong>. We will respond within a reasonable time.</p>
                </div>

                <div id="cookies" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">07</span> Cookies
                    </h2>
                    <p>Our website uses a small number of cookies and similar technologies, described in full in our <a href="{{ route('cookie-policy') }}" style="color:rgb(0,127,127); font-weight:700;">Cookie Policy</a>.</p>
                </div>

                <div id="security" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">08</span> Security
                    </h2>
                    <p>We apply reasonable technical and organisational measures to protect your personal data against unauthorised access, loss, or misuse, including encrypted password storage and access controls limiting who within our organisation can view client data. No method of transmission or storage is completely secure, and we cannot guarantee absolute security.</p>
                </div>

                <div id="children" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">09</span> Children
                    </h2>
                    <p>Our services are directed at businesses and adult consumers. We do not knowingly collect personal data from children.</p>
                </div>

                <div id="changes" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">10</span> Changes to This Policy
                    </h2>
                    <p>We may update this Privacy Policy from time to time. Material changes will be notified via email or a notice on our website. The "Last updated" date at the top of this page reflects the most recent revision.</p>
                </div>

                <div id="contact" style="background:#f0fafa; border-radius:16px; padding:2rem; border:1px solid #b2d8d8;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">
                        <span aria-hidden="true" style="color:#64748b; font-weight:900; margin-right:.5rem;">11</span> Contact &amp; Complaints
                    </h2>
                    <p style="margin-bottom:1rem;">For any question about this Policy or to exercise your data rights, contact us:</p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div style="width:40px; height:40px; border-radius:50%; background:rgb(0,127,127); display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0;">
                                <i class="fas fa-envelope" style="font-size:.9rem;"></i>
                            </div>
                            <div>
                                <p style="font-size:.75rem; color:#475569; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Email</p>
                                <p style="font-weight:700; color:#1e293b;">info@forusfl.co.zm</p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div style="width:40px; height:40px; border-radius:50%; background:rgb(0,127,127); display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0;">
                                <i class="fas fa-phone" style="font-size:.9rem;"></i>
                            </div>
                            <div>
                                <p style="font-size:.75rem; color:#475569; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Phone</p>
                                <p style="font-weight:700; color:#1e293b;">+260 572 788 685</p>
                            </div>
                        </div>
                    </div>
                    <p style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid #b2d8d8; font-size:.85rem; color:#475569;">
                        If you are not satisfied with our response, you may lodge a complaint with the <strong>Office of the Data Protection Commissioner of Zambia</strong>.
                    </p>
                </div>

            </article>
        </div>
    </div>
</section>

@endsection
