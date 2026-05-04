@extends('layouts.app')

@section('title', 'Track Your Shipment - Forus Freight')

@section('content')
<section style="padding: 4rem 0; background: linear-gradient(135deg, #007f7f 0%, #005f5f 100%);">
    <div class="container">
        <div style="max-width: 720px; margin:auto; text-align:center; color:white;">
            <h1 style="font-size:3rem; font-weight:800; margin-bottom: 1rem;">Track Your Shipment</h1>
            <p style="opacity:0.9; font-size:1.1rem;">Enter your tracking number to see live shipment progress</p>
        </div>
    </div>
</section>

<section style="padding:5rem 0; background: #f8fafc;">
    <div class="container">
        <div style="max-width:850px; margin:auto; background:white; border-radius:24px; padding:3rem; box-shadow:0 20px 50px rgba(0,0,0,.12);">

            <!-- Error/Success Messages -->
            @if(session('error'))
                <div style="margin-bottom: 2rem; padding: 1rem; background: rgba(239, 68, 68, 0.1); border-radius: 8px; border-left: 4px solid #ef4444;">
                    <p style="color: #ef4444; margin: 0; font-weight: 600;">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('info'))
                <div style="margin-bottom: 2rem; padding: 1rem; background: rgba(0, 127, 127, 0.1); border-radius: 8px; border-left: 4px solid #007f7f;">
                    <p style="color: #007f7f; margin: 0; font-weight: 600;">{{ session('info') }}</p>
                </div>
            @endif

            <!-- TRACKING FORM -->
            <form method="POST" action="{{ route('track.check') }}">
                @csrf
                <label style="font-weight:700; color:#1e293b; display:block; margin-bottom:0.5rem;">Tracking Number</label>
                <div style="display:flex; gap:1rem; margin-top:.5rem;">
                    <input type="text" name="tracking_number" placeholder="SWIFT-2023-00123"
                        value="{{ old('tracking_number', session('tracking_attempt') ?? '') }}"
                        style="flex:1; padding:1rem; border-radius:12px; border:2px solid #007f7f; font-size:1rem;">
                    <button type="submit" style="padding:1rem 2rem; border-radius:12px; font-weight:700; color:white; background: #ff6200; border:none; cursor:pointer; transition:all 0.3s;">
                        <i class="fas fa-search" style="margin-right: 0.5rem;"></i> Track Shipment
                    </button>
                </div>
                @error('tracking_number')
                    <p style="color: #ef4444; margin-top: 0.5rem; font-size: 0.875rem;">{{ $message }}</p>
                @enderror
            </form>

            <!-- Results for Authenticated Users -->
            @auth
                @if(isset($shipment) && $shipment)
                <div id="results" style="margin-top:3rem; padding:2rem; background:linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius:12px; border: 2px solid #007f7f;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="font-weight:700; color:#1e293b; margin:0;">Shipment Details</h3>
                        <span style="padding: 0.5rem 1rem; background: #007f7f; color: white; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                            {{ strtoupper($shipment->status) }}
                        </span>
                    </div>
                    
                    <!-- Shipment Info -->
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                        <div>
                            <p style="color: #64748b; margin-bottom: 0.25rem; font-size: 0.875rem;">Tracking Number</p>
                            <p style="color: #1e293b; font-weight: 600; font-size: 1.25rem;">{{ $shipment->tracking_number }}</p>
                        </div>
                        <div>
                            <p style="color: #64748b; margin-bottom: 0.25rem; font-size: 0.875rem;">Shipment Date</p>
                            <p style="color: #1e293b; font-weight: 600;">{{ $shipment->shipment_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p style="color: #64748b; margin-bottom: 0.25rem; font-size: 0.875rem;">From</p>
                            <p style="color: #1e293b; font-weight: 600;">{{ $shipment->origin }}</p>
                        </div>
                        <div>
                            <p style="color: #64748b; margin-bottom: 0.25rem; font-size: 0.875rem;">To</p>
                            <p style="color: #1e293b; font-weight: 600;">{{ $shipment->destination }}</p>
                        </div>
                    </div>
                    
                    <!-- Tracking Timeline -->
                    <div style="margin-top: 2rem;">
                        <h4 style="font-weight:600; color:#1e293b; margin-bottom:1.5rem;">Shipment Timeline</h4>
                        <div style="position:relative; padding-left:2rem;">
                            @foreach($shipment->trackingEvents as $index => $event)
                            <div style="position:relative; margin-bottom:2rem;">
                                <div style="position:absolute; left:-8px; top:0; width:16px; height:16px; 
                                    background: {{ $index == count($shipment->trackingEvents) - 1 ? '#ff6200' : '#007f7f' }}; 
                                    border-radius:50%;"></div>
                                <div style="padding-left:1rem;">
                                    <p style="font-weight:600; color:#1e293b; margin-bottom:0.25rem;">{{ $event->description }}</p>
                                    <p style="color:#64748b; font-size:0.9rem;">
                                        {{ $event->location }} • {{ $event->event_time->format('M d, Y • h:i A') }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                            <!-- Timeline line -->
                            <div style="position:absolute; left:0; top:8px; bottom:8px; width:2px; background:#cccccc;"></div>
                        </div>
                    </div>
                    
                    <!-- Estimated Delivery -->
                    <div style="margin-top: 2rem; padding: 1.5rem; background: white; border-radius: 8px; border: 2px solid #059669;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <i class="fas fa-truck" style="color: #059669; font-size: 1.5rem;"></i>
                            <div>
                                <p style="font-weight: 600; color: #1e293b; margin-bottom: 0.25rem;">Estimated Delivery</p>
                                <p style="color: #64748b;">
                                    {{ $shipment->estimated_delivery->format('l, F j, Y') }} • 
                                    <span style="color: #059669; font-weight: 600;">By {{ $shipment->estimated_delivery->format('h:i A') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endauth

            <!-- Demo Numbers Section (only show if not authenticated or no shipment) -->
            @if(!isset($shipment) || !$shipment)
            <div style="margin-top:3rem; padding:2rem; background:linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%); border-radius:12px; border: 2px solid #cccccc;">
                <h4 style="font-weight:700; color:#1e293b; margin-bottom:1rem;">Demo Tracking Numbers</h4>
                <p style="color:#64748b; margin-bottom:1.5rem;">Try these example tracking numbers to see how our tracking system works:</p>
                <div style="display:flex; flex-wrap:wrap; gap:1rem;">
                    <button onclick="document.querySelector('input[name=\"tracking_number\"]').value='SWIFT-2023-00123'; document.querySelector('form').submit();" 
                            style="padding:0.75rem 1.5rem; background:white; border:2px solid #007f7f; border-radius:8px; color:#007f7f; font-weight:600; cursor:pointer; transition:all 0.3s;">
                        SWIFT-2023-00123
                    </button>
                    <button onclick="document.querySelector('input[name=\"tracking_number\"]').value='SWIFT-2023-00456'; document.querySelector('form').submit();" 
                            style="padding:0.75rem 1.5rem; background:white; border:2px solid #007f7f; border-radius:8px; color:#007f7f; font-weight:600; cursor:pointer; transition:all 0.3s;">
                        SWIFT-2023-00456
                    </button>
                    <button onclick="document.querySelector('input[name=\"tracking_number\"]').value='SWIFT-2023-00789'; document.querySelector('form').submit();" 
                            style="padding:0.75rem 1.5rem; background:white; border:2px solid #007f7f; border-radius:8px; color:#007f7f; font-weight:600; cursor:pointer; transition:all 0.3s;">
                        SWIFT-2023-00789
                    </button>
                </div>
            </div>
            @endif

            <!-- Contact Support -->
            <div style="margin-top: 3rem; padding: 2rem; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 12px; color: white;">
                <div style="display: flex; align-items: start; gap: 1.5rem;">
                    <div style="flex-shrink: 0;">
                        <i class="fas fa-headset" style="color: #059669; font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h4 style="font-weight: 700; margin-bottom: 0.75rem;">Need Help With Your Shipment?</h4>
                        <p style="color: rgba(255,255,255,0.9); margin-bottom: 1.5rem;">
                            Our customer support team is available 24/7 to assist you with any questions about your shipment.
                        </p>
                        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-phone" style="color: #059669;"></i>
                                <span>+260 96 123 4567</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fab fa-whatsapp" style="color: #059669;"></i>
                                <span>WhatsApp: +260 96 123 4567</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-envelope" style="color: #059669;"></i>
                                <span>support@forusfreight.co.zm</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    input:focus {
        outline: none;
        border-color: #ff6200 !important;
        box-shadow: 0 0 0 3px rgba(0,127,127,0.1);
    }
    
    .container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    @media (max-width: 768px) {
        section > .container > div[style*="max-width:850px"] {
            padding: 2rem !important;
        }
        
        div[style*="display:flex; gap:1rem;"] {
            flex-direction: column;
        }
        
        button[style*="padding:1rem 2rem;"] {
            width: 100%;
        }
        
        h1[style*="font-size:3rem"] {
            font-size: 2rem !important;
        }
        
        div[style*="grid-template-columns: repeat(2, 1fr)"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

@endsection