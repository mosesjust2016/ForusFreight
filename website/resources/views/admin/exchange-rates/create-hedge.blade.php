@extends('layouts.dashboard')

@section('title', 'Create Currency Hedge - Forus Freight')

@section('styles')
<style>
    .hedge-form {
        max-width: 700px;
        margin: 0 auto;
    }
    .form-card {
        background: white;
        border-radius: 24px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    .form-control {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        transition: all 0.3s;
        font-family: inherit;
    }
    .form-control:focus {
        outline: none;
        border-color: #007f7f;
        box-shadow: 0 0 0 4px rgba(0,127,127,0.1);
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .rate-preview {
        background: linear-gradient(135deg, #f0f9f9 0%, #e0f2f2 100%);
        border: 2px solid #007f7f;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .rate-preview h4 {
        color: #007f7f;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.5rem;
    }
    .rate-preview .value {
        font-size: 2rem;
        font-weight: 900;
        color: #1e293b;
    }
    .btn-submit {
        background: #ff6200;
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        width: 100%;
    }
    .btn-submit:hover {
        background: #e65800;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255,98,0,0.3);
    }
    .calculated {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: #007f7f;
        text-align: center;
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="welcome-section" style="margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.25rem;">Create Currency Hedge</h1>
        <p style="color: var(--text-gray); font-size: 0.9rem;">Lock in an exchange rate for a client's future shipment payment.</p>
    </div>
</div>

<div class="hedge-form">
    @if($latestRate)
    <div class="rate-preview">
        <h4><i class="fas fa-building-columns"></i> Current BOZ Rate</h4>
        <div class="value">{{ number_format($latestRate->mid_rate, 4) }} ZMW</div>
        <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.5rem;">1 USD = {{ number_format($latestRate->mid_rate, 4) }} ZMW</p>
    </div>
    @endif

    <div class="form-card">
        <form action="{{ route('admin.exchange-rates.hedge.store') }}" method="POST" x-data="{ amount: 0, rate: {{ $latestRate ? $latestRate->mid_rate : '0' }}, get zmw() { return this.amount && this.rate ? (this.amount * this.rate).toFixed(2) : '0.00'; } }">
            @csrf

            <div class="form-group">
                <label>Client</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Select a client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email ?: 'no email on file' }})</option>
                    @endforeach
                </select>
                @error('user_id')<p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Link to Shipment (Optional)</label>
                <select name="shipment_id" class="form-control">
                    <option value="">No shipment yet...</option>
                    @foreach($shipments as $shipment)
                        <option value="{{ $shipment->id }}">{{ $shipment->serial_no }} - {{ $shipment->origin }} to {{ $shipment->destination }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Amount (USD)</label>
                    <input type="number" name="amount_usd" class="form-control" step="0.01" min="0.01" x-model="amount" required placeholder="0.00">
                    @error('amount_usd')<p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Hedged Rate (ZMW per USD)</label>
                    <input type="number" name="hedged_rate" class="form-control" step="0.0001" min="0.01" x-model="rate" required placeholder="0.0000">
                    @error('hedged_rate')<p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Calculated ZMW Amount</label>
                <div class="calculated">
                    ZMW <span x-text="zmw"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Hedge Date</label>
                    <input type="date" name="hedge_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    @error('hedge_date')<p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                    @error('expiry_date')<p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Any additional notes..."></textarea>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-shield-halved" style="margin-right: 0.5rem;"></i> Create Hedge
            </button>
        </form>
    </div>
</div>
@endsection
