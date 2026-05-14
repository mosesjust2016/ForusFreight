@extends('layouts.dashboard')

@section('title', 'Secure Checkout - Forus Freight')

@section('styles')
<style>
    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 3rem;
    }

    .payment-card {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: var(--shadow);
        border: 1px solid #f1f5f9;
    }

    .payment-method {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        border: 2px solid #f1f5f9;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 1.25rem;
        position: relative;
    }

    .payment-method:hover {
        border-color: var(--primary-green);
        background: var(--primary-green-light);
    }

    .payment-method.active {
        border-color: var(--primary-green);
        background: var(--primary-green-light);
    }

    .payment-method input[type="radio"] {
        position: absolute;
        right: 1.5rem;
        width: 20px;
        height: 20px;
        accent-color: var(--primary-green);
    }

    .method-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .method-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .method-info h4 {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 0.25rem;
    }

    .method-info p {
        font-size: 0.8rem;
        color: var(--text-gray);
        font-weight: 600;
    }

    .summary-card {
        background: #1e293b;
        color: white;
        border-radius: 30px;
        padding: 2.5rem;
        position: sticky;
        top: 2rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        font-size: 0.9rem;
        color: #94a3b8;
    }

    .summary-total {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255,255,255,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 3.5rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Secure Payment</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Complete your transaction using our secure local payment gateways.</p>
    </div>
</div>

<div class="checkout-grid">
    <div class="payment-card">
        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 2rem; color: var(--text-dark);">Choose Payment Method</h3>
        
        <form action="#" method="POST" id="paymentForm">
            @csrf
            
            <!-- Airtel Money -->
            <label class="payment-method" onclick="selectMethod('airtel')">
                <div class="method-icon" style="background: #e11900;">
                    <i class="fas fa-mobile-screen-button" style="color: white; font-size: 1.5rem;"></i>
                </div>
                <div class="method-info">
                    <h4>Airtel Money</h4>
                    <p>Instant mobile money payment</p>
                </div>
                <input type="radio" name="payment_method" value="airtel" required>
            </label>

            <!-- MTN Momo -->
            <label class="payment-method" onclick="selectMethod('mtn')">
                <div class="method-icon" style="background: #ffcc00;">
                    <i class="fas fa-mobile-button" style="color: #003399; font-size: 1.5rem;"></i>
                </div>
                <div class="method-info">
                    <h4>MTN MoMo</h4>
                    <p>Secure mobile wallet transaction</p>
                </div>
                <input type="radio" name="payment_method" value="mtn">
            </label>

            <!-- Credit Card -->
            <label class="payment-method" onclick="selectMethod('card')">
                <div class="method-icon" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <i class="fas fa-credit-card" style="color: #64748b; font-size: 1.5rem;"></i>
                </div>
                <div class="method-info">
                    <h4>Visa / Mastercard</h4>
                    <p>Debit or Credit Card</p>
                </div>
                <input type="radio" name="payment_method" value="card">
            </label>

            <!-- Bank Transfer -->
            <label class="payment-method" onclick="selectMethod('bank')">
                <div class="method-icon" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <i class="fas fa-building-columns" style="color: #64748b; font-size: 1.5rem;"></i>
                </div>
                <div class="method-info">
                    <h4>Bank Transfer</h4>
                    <p>Manual EFT / Proof of Payment</p>
                </div>
                <input type="radio" name="payment_method" value="bank">
            </label>

            <div style="margin-top: 3rem;">
                <button type="submit" style="width: 100%; padding: 1.5rem; background: var(--primary-green); color: white; border: none; border-radius: 20px; font-weight: 900; font-size: 1.1rem; cursor: pointer; box-shadow: 0 15px 30px rgba(76, 175, 80, 0.3); display: flex; align-items: center; justify-content: center; gap: 1rem;">
                    PROCEED TO PAY {{ number_format($invoice->amount, 2) }} {{ $invoice->currency }} <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="summary-card">
        <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 2rem; color: white;">Order Summary</h3>
        
        <div class="summary-item">
            <span>Invoice Number</span>
            <span style="color: white; font-weight: 800;">#INV-{{ $invoice->invoice_number }}</span>
        </div>

        @if($invoice->shipment)
        <div class="summary-item">
            <span>Tracking Reference</span>
            <span style="color: var(--primary-green); font-weight: 800;">#{{ $invoice->shipment->tracking_number }}</span>
        </div>
        <div class="summary-item">
            <span>Route</span>
            <span style="color: white; font-weight: 600;">{{ $invoice->shipment->origin }} → {{ $invoice->shipment->destination }}</span>
        </div>
        @endif

        <div class="summary-item">
            <span>Tax (VAT 16%)</span>
            <span style="color: white; font-weight: 600;">Included</span>
        </div>

        <div class="summary-total">
            <div>
                <p style="font-size: 0.75rem; color: #94a3b8; font-weight: 800; text-transform: uppercase;">Total Payable</p>
                <h2 style="font-size: 1.75rem; font-weight: 900; color: white; margin-top: 0.25rem;">{{ number_format($invoice->amount, 2) }} <span style="font-size: 0.9rem; font-weight: 600; opacity: 0.7;">{{ $invoice->currency }}</span></h2>
            </div>
            <i class="fas fa-shield-halved" style="font-size: 2rem; color: var(--primary-green); opacity: 0.5;"></i>
        </div>

        <div style="margin-top: 2.5rem; padding: 1.25rem; background: rgba(255,255,255,0.05); border-radius: 15px; display: flex; gap: 1rem; align-items: center;">
            <i class="fas fa-lock" style="color: var(--primary-green);"></i>
            <p style="font-size: 0.75rem; color: #94a3b8; line-height: 1.4;">Your transaction is protected by 256-bit SSL encryption and secure local banking protocols.</p>
        </div>
    </div>
</div>

<script>
    function selectMethod(method) {
        // Remove active class from all
        document.querySelectorAll('.payment-method').forEach(el => {
            el.classList.remove('active');
        });
        
        // Add active class to clicked one
        const el = event.currentTarget;
        el.classList.add('active');
        
        // Check the radio
        el.querySelector('input[type="radio"]').checked = true;
    }
</script>
@endsection
