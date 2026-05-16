<?php

use App\Services\SmsService;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $otp = '';
    public bool $resent = false;

    public function mount(): void
    {
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
            $this->redirect(route('dashboard'), navigate: true);
        } else {
            $this->addError('otp', 'The code is invalid or has expired. Please try again.');
        }
    }

    public function resend(): void
    {
        $user = Auth::user();
        $otp = $user->generatePhoneOtp();

        app(SmsService::class)->sendOtp($user->phone, $otp);

        $this->resent = true;
        $this->otp = '';
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }
}; 
?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Verify Phone</h2>
        <p class="mt-3 text-sm text-slate-500">
            Enter the 6-digit code sent to <strong>{{ Auth::user()->phone }}</strong>
        </p>
    </div>

    @if (app()->environment('local'))
        <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-2xl text-sm font-medium text-center">
            🛠️ <strong>Dev Mode:</strong> Use <strong>123456</strong> to bypass SMS verification.
        </div>
    @endif

    @if ($resent)
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium text-center animate-fade-in flex items-center justify-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>
            A new verification code has been sent to your phone.
        </div>
    @endif

    <div class="bg-white p-8 shadow-2xl shadow-slate-200/50 rounded-3xl border border-slate-100 transition-all duration-300">
        <form wire:submit="verify" class="space-y-6">
            <div>
                <label for="otp" class="block text-sm font-bold text-slate-700 mb-2">Verification Code</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-mobile-screen-button"></i>
                    </div>
                    <input wire:model="otp" id="otp"
                        class="form-control pl-11"
                        type="text" name="otp" required
                        maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                        placeholder="000000"
                        style="letter-spacing: 0.5rem; font-size: 1.25rem; text-align: center;" />
                </div>
                <x-input-error :messages="$errors->get('otp')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                <span wire:loading.remove wire:target="verify">Verify Phone</span>
                <span wire:loading wire:target="verify" class="flex items-center gap-2">
                    <i class="fas fa-circle-notch animate-spin"></i>
                    Verifying...
                </span>
                <i wire:loading.remove wire:target="verify" class="fas fa-check text-xs opacity-50 group-hover:scale-110 transition-transform"></i>
            </button>
        </form>

        <div class="mt-6 space-y-4">
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
                class="text-center">
                
                <p class="text-sm text-slate-500 mb-2">Didn't receive the code?</p>
                
                <button wire:click="resend" 
                        x-on:click="startTimer()"
                        x-bind:disabled="timeLeft > 0"
                        class="text-sm font-bold text-[rgb(0,127,127)] hover:text-[rgb(255,98,0)] transition-colors disabled:opacity-50 disabled:cursor-not-allowed disabled:no-underline underline">
                    <span wire:loading.remove wire:target="resend">
                        <span x-show="timeLeft === 0">Resend Code</span>
                        <span x-show="timeLeft > 0" x-text="'Resend Code in ' + formattedTime"></span>
                    </span>
                    <span wire:loading wire:target="resend">Sending...</span>
                </button>
            </div>

            <div class="text-center pt-4 border-t border-slate-100">
                <button wire:click="logout" type="button" class="text-sm font-bold text-slate-400 hover:text-[rgb(255,98,0)] transition-colors">
                    <i class="fas fa-sign-out-alt mr-1"></i>
                    Log Out
                </button>
            </div>
        </div>
    </div>
</div>
