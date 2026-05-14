<?php

use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $otp = '';
    public bool $resent = false;

    public function mount(): void
    {
        // Already verified — go to dashboard
        if (Auth::user()->hasVerifiedPhone()) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function verify(): void
    {
        $this->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();

        if ($user->verifyPhoneOtp($this->otp)) {
            // Both verifications done — redirect to dashboard
            // Email verification notice if email not yet verified
            if (! $user->hasVerifiedEmail()) {
                $this->redirect(route('verification.notice'), navigate: true);
            } else {
                $this->redirect(route('dashboard'), navigate: true);
            }
        } else {
            $this->addError('otp', 'The code is invalid or has expired. Please try again.');
        }
    }

    public function resend(): void
    {
        $user = Auth::user();
        $otp  = $user->generatePhoneOtp();

        app(SmsService::class)->sendOtp($user->phone, $otp);

        $this->resent = true;
        $this->otp    = '';
    }
}; 
?>

<div>
    <h2>Verify Your Phone</h2>
    <p class="subtitle">
        We sent a 6-digit code to
        <strong>{{ Auth::user()->phone }}</strong>.
        Enter it below to continue.
    </p>

    @if (app()->environment('local'))
        <div style="background: #fffbeb; border: 1px solid #fef3c7; color: #b45309; padding: 0.5rem; border-radius: 0.5rem; font-size: 0.8rem; text-align: center; margin-bottom: 1rem;">
            🛠️ <strong>Dev Mode:</strong> You can use <strong>123456</strong> to bypass SMS verification.
        </div>
    @endif

    @if ($resent)
        <div class="auth-success">A new code has been sent to your phone!</div>
    @endif

    <form wire:submit="verify" class="auth-form" style="margin-top:1.5rem;">
        <div>
            <input wire:model="otp"
                   id="otp" type="text" name="otp"
                   placeholder="6-digit verification code"
                   class="auth-input"
                   maxlength="6"
                   inputmode="numeric"
                   pattern="[0-9]{6}"
                   required
                   style="letter-spacing: 0.5rem; font-size: 1.25rem; text-align: center;" />
            @error('otp')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-btn-primary">
            <span wire:loading.remove wire:target="verify">Verify Phone Number</span>
            <span wire:loading wire:target="verify">Verifying...</span>
        </button>
    </form>

    <div x-data="{ 
            timeLeft: 180, 
            timer: null, 
            startTimer() { 
                this.timeLeft = 180; 
                clearInterval(this.timer);
                this.timer = setInterval(() => { 
                    if(this.timeLeft > 0) this.timeLeft--; 
                    else clearInterval(this.timer); 
                }, 1000); 
            },
            get formattedTime() {
                let m = Math.floor(this.timeLeft / 60);
                let s = this.timeLeft % 60;
                return m + ':' + (s < 10 ? '0' : '') + s;
            }
        }" 
        x-init="startTimer()"
        style="text-align:center; margin-top:1.25rem;">
        
        <p style="font-size:0.875rem; color:#6b7280; margin-bottom:0.5rem;">Didn't receive the code?</p>
        
        <button wire:click="resend" 
                x-on:click="startTimer()"
                x-bind:disabled="timeLeft > 0"
                style="background:none; border:none; color:#00706f; font-weight:600; font-size:0.875rem; text-decoration:underline; padding:0; transition: opacity 0.2s;"
                x-bind:style="timeLeft > 0 ? 'opacity: 0.5; cursor: not-allowed; text-decoration: none;' : 'opacity: 1; cursor: pointer; text-decoration: underline;'">
            
            <span wire:loading.remove wire:target="resend">
                <span x-show="timeLeft === 0">Resend Code</span>
                <span x-show="timeLeft > 0" x-text="'Resend Code in ' + formattedTime"></span>
            </span>
            <span wire:loading wire:target="resend">Sending...</span>
        </button>
    </div>

    <div style="margin-top:1.5rem; background:#f0fdf9; border:1px solid #a7f3e0; border-radius:0.5rem; padding:0.75rem 1rem;">
        <p style="font-size:0.8rem; color:#065f46; font-weight:500; margin-bottom:0.25rem;">📧 Also check your email</p>
        <p style="font-size:0.8rem; color:#065f46;">
            We also sent a verification link to <strong>{{ Auth::user()->email }}</strong>.
            You'll need to verify your email address as well.
        </p>
    </div>
</div>
