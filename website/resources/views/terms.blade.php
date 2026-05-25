@php
$page = \App\Models\CmsPage::where('slug', 'terms')->first();
$sections = $page?->sections ?? [];
@endphp
@extends('layouts.app')

@section('title', ($page?->title ?? 'Terms & Conditions') . ' - Forus Freight')

@section('content')

<!-- HERO -->
<section style="padding: 5rem 0; background: linear-gradient(135deg, rgb(0,127,127), #004c4c);">
    <div class="container">
        <div style="max-width: 760px; margin: auto; text-align: center; color: #fff;">
            <div style="display:inline-flex; align-items:center; gap:.5rem; background:rgba(255,255,255,.15); padding:.4rem 1rem; border-radius:50px; font-size:.8rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:1.5rem;">
                <i class="fas fa-file-contract"></i> Legal
            </div>
            <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 1rem; line-height:1.15;">{{ $sections['title'] ?? 'Terms & Conditions' }}</h1>
            <p style="opacity:.85; font-size:1.05rem;">
                {{ $sections['subtitle'] ?? 'These terms govern the use of Forus Freight Limited\'s services. Please read them carefully before booking a shipment.' }}
            </p>
            <p style="opacity:.65; font-size:.85rem; margin-top:1rem;">Last updated: {{ date('F j, Y') }}</p>
        </div>
    </div>
</section>

<!-- CONTENT -->
<section style="padding: 5rem 0; background: #f8fafc;">
    <div class="container">
        <div style="max-width: 900px; margin: auto; display: grid; grid-template-columns: 260px 1fr; gap: 3rem; align-items: start;">

            <!-- Sticky Table of Contents -->
            <aside style="position: sticky; top: 90px; background: #fff; border-radius: 20px; padding: 1.75rem; box-shadow: 0 4px 24px rgba(0,0,0,.07); border: 1px solid #e2e8f0;">
                <p style="font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#94a3b8; margin-bottom:1rem;">Contents</p>
                <nav style="display:flex; flex-direction:column; gap:.35rem;">
                    @foreach([
                        ['#definitions',        'Definitions'],
                        ['#services',           'Our Services'],
                        ['#booking',            'Booking & Orders'],
                        ['#rates',              'Rates & Payment'],
                        ['#liability',          'Liability'],
                        ['#prohibited',         'Prohibited Goods'],
                        ['#dangerous',          'Dangerous Goods'],
                        ['#insurance',          'Insurance'],
                        ['#claims',             'Claims'],
                        ['#customs',            'Customs & Compliance'],
                        ['#force-majeure',      'Force Majeure'],
                        ['#data',               'Data & Privacy'],
                        ['#termination',        'Termination'],
                        ['#governing-law',      'Governing Law'],
                        ['#contact',            'Contact Us'],
                    ] as [$href, $label])
                    <a href="{{ $href }}"
                       style="font-size:.85rem; color:#475569; text-decoration:none; padding:.3rem .6rem; border-radius:8px; transition:.2s;"
                       onmouseover="this.style.background='rgb(0,127,127)'; this.style.color='#fff';"
                       onmouseout="this.style.background='transparent'; this.style.color='#475569';">
                        {{ $label }}
                    </a>
                    @endforeach
                </nav>
            </aside>

            <!-- Main Content -->
            <article style="background: #fff; border-radius: 24px; padding: 3rem; box-shadow: 0 4px 24px rgba(0,0,0,.07); border: 1px solid #e2e8f0; color: #334155; line-height: 1.8; font-size: .97rem;">

                {!! $sections['content'] ?? '<p style="color:#64748b; margin-bottom:2.5rem;">
                    Welcome to <strong>Forus Freight Limited</strong> ("Forus Freight", "we", "us", or "our"), a logistics and freight company registered in Zambia. By booking, using, or accessing any of our services you agree to be bound by these Terms and Conditions.
                </p>' !!}

                {{-- 1. Definitions --}}
                <div id="definitions" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">01</span> Definitions
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li><strong>Consignment / Shipment</strong> — any goods, cargo, packages, or documents accepted by Forus Freight for transportation.</li>
                        <li><strong>Shipper</strong> — the individual or entity that tenders a consignment to Forus Freight for delivery.</li>
                        <li><strong>Consignee</strong> — the individual or entity named as the recipient of a consignment.</li>
                        <li><strong>Waybill / Air Waybill (AWB)</strong> — the shipping document issued by Forus Freight serving as a contract of carriage.</li>
                        <li><strong>Dangerous Goods</strong> — items classified as hazardous under IATA, IMDG, or applicable Zambian regulations.</li>
                        <li><strong>Force Majeure</strong> — events beyond the reasonable control of either party including acts of God, war, strikes, or government action.</li>
                        <li><strong>SADC</strong> — the Southern African Development Community region.</li>
                    </ul>
                </div>

                {{-- 2. Services --}}
                <div id="services" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">02</span> Our Services
                    </h2>
                    <p style="margin-bottom:.8rem;">Forus Freight provides the following logistics services, subject to these Terms:</p>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Road freight — domestic and cross-border within Zambia and the SADC region.</li>
                        <li>Air freight — international export and import cargo handling.</li>
                        <li>Sea freight — full container load (FCL) and less-than-container load (LCL) services.</li>
                        <li>Customs brokerage and clearance.</li>
                        <li>Warehousing and distribution.</li>
                        <li>Real-time shipment tracking via our online portal.</li>
                        <li>Last-mile delivery within Lusaka and major Zambian cities.</li>
                    </ul>
                    <p style="margin-top:.8rem;">Forus Freight reserves the right to sub-contract any part of the service to reputable carriers while remaining the principal contractor.</p>
                </div>

                {{-- 3. Booking --}}
                <div id="booking" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">03</span> Booking &amp; Acceptance of Orders
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>All bookings must be submitted via our online portal, email, or authorised agents. Verbal bookings are not binding.</li>
                        <li>A booking is confirmed only upon receipt of a written confirmation or waybill issued by Forus Freight.</li>
                        <li>The Shipper warrants that all information provided (description, weight, dimensions, value, and destination) is accurate and complete. Any misrepresentation may result in additional charges, refusal of service, or legal liability.</li>
                        <li>Forus Freight may refuse any consignment at its sole discretion without liability.</li>
                        <li>Booking confirmation does not guarantee a specific transit time unless expressly stated in writing.</li>
                    </ul>
                </div>

                {{-- 4. Rates & Payment --}}
                <div id="rates" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">04</span> Rates, Charges &amp; Payment
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>All rates are quoted in Zambian Kwacha (ZMW) unless otherwise stated. Foreign currency quotes are subject to exchange rate fluctuations and will be confirmed at invoice date.</li>
                        <li>Quoted rates are valid for 7 calendar days from the date of issue unless otherwise specified.</li>
                        <li>Chargeable weight is the greater of actual gross weight and volumetric weight (L × W × H ÷ 5,000 for air freight; ÷ 3,000 for road).</li>
                        <li>Payment is due within 30 days of invoice date for credit-approved clients. All other clients must pay prior to release of goods.</li>
                        <li>Late payments attract interest at 5% per month or the prevailing Bank of Zambia lending rate, whichever is higher.</li>
                        <li>Forus Freight exercises a lien over all goods in its possession until all outstanding charges are settled.</li>
                        <li>Additional charges may apply for re-delivery, failed deliveries, storage beyond 48 hours, or special handling requirements.</li>
                        <li>Taxes, customs duties, and port charges are the sole responsibility of the Shipper or Consignee as applicable.</li>
                    </ul>
                </div>

                {{-- 5. Liability --}}
                <div id="liability" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">05</span> Liability &amp; Limitation of Liability
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Forus Freight's maximum liability for loss or damage is limited to <strong>ZMW 50 per kilogram</strong> of the affected goods, or the declared value if a higher value has been declared in writing and an appropriate surcharge paid.</li>
                        <li>Forus Freight is not liable for: loss of income or profit, indirect or consequential losses, delays caused by customs authorities or third parties, or damage resulting from improper packaging by the Shipper.</li>
                        <li>Liability for international air shipments is governed by the <em>Warsaw Convention</em> or the <em>Montreal Convention</em> as applicable.</li>
                        <li>Liability for road freight within Zambia is governed by the <em>Road Traffic Act (Cap 464)</em> of Zambia.</li>
                        <li>Forus Freight is not liable for perishable goods that deteriorate during any delay unless negligence is proven.</li>
                    </ul>
                </div>

                {{-- 6. Prohibited Goods --}}
                <div id="prohibited" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">06</span> Prohibited Goods
                    </h2>
                    <p style="margin-bottom:.8rem;">The following goods are strictly prohibited from being tendered to Forus Freight:</p>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Illegal narcotics, drugs, and controlled substances.</li>
                        <li>Firearms, ammunition, and military equipment (without prior written authorisation).</li>
                        <li>Counterfeit currency, forged documents, and contraband.</li>
                        <li>Ivory, rhino horn, and any items prohibited under CITES regulations.</li>
                        <li>Human remains (without appropriate documentation and prior authorisation).</li>
                        <li>Any goods whose import or export is prohibited by Zambian law or the law of the destination country.</li>
                    </ul>
                    <p style="margin-top:.8rem;">If prohibited goods are tendered, Forus Freight reserves the right to dispose of them in accordance with applicable law and to charge the Shipper all costs incurred.</p>
                </div>

                {{-- 7. Dangerous Goods --}}
                <div id="dangerous" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">07</span> Dangerous Goods
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Dangerous goods (chemicals, flammables, explosives, etc.) may only be accepted with prior written approval from Forus Freight.</li>
                        <li>The Shipper must provide all required Safety Data Sheets (SDS), proper UN classification, and compliant packaging.</li>
                        <li>The Shipper is fully liable for any loss, injury, or regulatory penalty arising from undeclared or misdeclared dangerous goods.</li>
                        <li>Handling surcharges apply to all accepted dangerous goods shipments.</li>
                    </ul>
                </div>

                {{-- 8. Insurance --}}
                <div id="insurance" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">08</span> Insurance
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Forus Freight maintains basic carrier liability insurance. This does not replace all-risk cargo insurance.</li>
                        <li>Shippers are strongly advised to arrange their own all-risk cargo insurance for the full commercial value of their goods.</li>
                        <li>Forus Freight can arrange cargo insurance on behalf of the Shipper upon request, at an additional premium.</li>
                        <li>Insurance claims must be submitted in writing within 7 days of the delivery date or expected delivery date for lost cargo.</li>
                    </ul>
                </div>

                {{-- 9. Claims --}}
                <div id="claims" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">09</span> Claims for Loss or Damage
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Any damage visible at delivery must be noted on the delivery receipt and reported to Forus Freight within <strong>24 hours</strong>.</li>
                        <li>Claims for concealed damage must be submitted in writing within <strong>7 days</strong> of delivery.</li>
                        <li>Claims for non-delivery must be submitted within <strong>30 days</strong> of the scheduled delivery date.</li>
                        <li>Claims must be supported by the original waybill, commercial invoice, packing list, and photographic evidence where applicable.</li>
                        <li>No claim will be considered for goods accepted without exception noted at delivery, unless concealed damage is proven.</li>
                        <li>Payment of an invoice does not constitute a waiver of any claim.</li>
                    </ul>
                </div>

                {{-- 10. Customs --}}
                <div id="customs" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">10</span> Customs &amp; Regulatory Compliance
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>The Shipper is responsible for providing accurate and complete customs documentation including commercial invoices, certificates of origin, import/export permits, and any other required documents.</li>
                        <li>Forus Freight acts as the Shipper's agent for customs purposes only when expressly instructed to do so in writing.</li>
                        <li>Delays, fines, or penalties resulting from incorrect or incomplete documentation are solely the Shipper's responsibility.</li>
                        <li>Forus Freight complies with the Zambia Revenue Authority (ZRA), the Drug Enforcement Commission (DEC), and all relevant SADC cross-border regulations.</li>
                        <li>Goods may be inspected by customs authorities at any time. Forus Freight will cooperate fully with such inspections.</li>
                    </ul>
                </div>

                {{-- 11. Force Majeure --}}
                <div id="force-majeure" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">11</span> Force Majeure
                    </h2>
                    <p>Forus Freight shall not be liable for any failure or delay in performance caused by circumstances beyond its reasonable control, including but not limited to: acts of God (floods, earthquakes), war, terrorism, civil unrest, strikes, government embargoes, road closures, border shutdowns, pandemics, or fuel shortages. In such events, Forus Freight will notify affected clients as soon as practicable and will resume services as soon as the force majeure event ceases.</p>
                </div>

                {{-- 12. Data & Privacy --}}
                <div id="data" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">12</span> Data &amp; Privacy
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Forus Freight collects personal and shipment data solely to provide its logistics services and comply with legal obligations.</li>
                        <li>Data will not be sold to third parties. It may be shared with sub-contractors, customs authorities, and regulatory bodies as required.</li>
                        <li>By using our services, you consent to the collection, processing, and storage of your data in accordance with Zambian data protection laws.</li>
                        <li>You have the right to request access to, correction of, or deletion of your personal data by contacting us at <strong>info@forusfl.co.zm</strong>.</li>
                    </ul>
                </div>

                {{-- 13. Termination --}}
                <div id="termination" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">13</span> Account Suspension &amp; Termination
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>Forus Freight may suspend or terminate a client's account without notice if the client breaches these Terms, provides false information, or fails to pay outstanding invoices.</li>
                        <li>Upon termination, all outstanding balances become immediately due and payable.</li>
                        <li>The client may close their account at any time by contacting support, provided all outstanding shipments have been completed and all charges settled.</li>
                    </ul>
                </div>

                {{-- 14. Governing Law --}}
                <div id="governing-law" style="margin-bottom:3rem;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0;">
                        <span style="color:#e2e8f0; font-weight:900; margin-right:.5rem;">14</span> Governing Law &amp; Dispute Resolution
                    </h2>
                    <ul style="margin-left:1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                        <li>These Terms are governed by the laws of the Republic of Zambia.</li>
                        <li>Any dispute arising from these Terms or the provision of services shall first be referred to mediation. If unresolved within 30 days, it shall be referred to arbitration under the Zambia Centre for Dispute Resolution (ZCDR) rules.</li>
                        <li>The courts of Zambia shall have exclusive jurisdiction over any matter not resolved by arbitration.</li>
                        <li>Forus Freight reserves the right to amend these Terms at any time. Clients will be notified of material changes via email or the online portal. Continued use of services constitutes acceptance of updated Terms.</li>
                    </ul>
                </div>

                {{-- 15. Contact --}}
                <div id="contact" style="background:#f0fafa; border-radius:16px; padding:2rem; border:1px solid #b2d8d8;">
                    <h2 style="font-size:1.4rem; font-weight:800; color:rgb(0,127,127); margin-bottom:1rem;">
                        <span style="color:#b2d8d8; font-weight:900; margin-right:.5rem;">15</span> Contact Us
                    </h2>
                    <p style="margin-bottom:1rem;">For questions about these Terms, please contact our legal or compliance team:</p>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div style="width:40px; height:40px; border-radius:50%; background:rgb(0,127,127); display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0;">
                                <i class="fas fa-envelope" style="font-size:.9rem;"></i>
                            </div>
                            <div>
                                <p style="font-size:.75rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Email</p>
                                <p style="font-weight:700; color:#1e293b;">info@forusfl.co.zm</p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div style="width:40px; height:40px; border-radius:50%; background:rgb(0,127,127); display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0;">
                                <i class="fas fa-phone" style="font-size:.9rem;"></i>
                            </div>
                            <div>
                                <p style="font-size:.75rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Phone</p>
                                <p style="font-weight:700; color:#1e293b;">+260 572 788 6857</p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div style="width:40px; height:40px; border-radius:50%; background:rgb(0,127,127); display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0;">
                                <i class="fas fa-location-dot" style="font-size:.9rem;"></i>
                            </div>
                            <div>
                                <p style="font-size:.75rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Address</p>
                                <p style="font-weight:700; color:#1e293b;">Lusaka, Zambia</p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div style="width:40px; height:40px; border-radius:50%; background:rgb(0,127,127); display:flex; align-items:center; justify-content:center; color:#fff; flex-shrink:0;">
                                <i class="fas fa-clock" style="font-size:.9rem;"></i>
                            </div>
                            <div>
                                <p style="font-size:.75rem; color:#94a3b8; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Hours</p>
                                <p style="font-weight:700; color:#1e293b;">Mon–Fri, 08:00–17:00 CAT</p>
                            </div>
                        </div>
                    </div>
                </div>

            </article>
        </div>
    </div>
</section>

@endsection
