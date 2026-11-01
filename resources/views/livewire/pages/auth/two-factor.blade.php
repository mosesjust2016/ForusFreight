<?php

use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $otp = '';

    public function verify(): void
    {
        $this->validate(['otp' => ['required', 'digits:6']]);

        $user = User::find(session('two_factor_user_id'));
        if (!$user || !$user->two_factor_enabled || !$user->verifyEmailOtp($this->otp)) {
            $this->addError('otp', 'That code is invalid or has expired.');
            return;
        }

        $guard = session('two_factor_guard', 'web');
        session()->forget(['two_factor_user_id', 'two_factor_guard']);
        Auth::guard($guard)->login($user, true);
        session()->regenerate();
        $defaultRoute = $guard === 'admin' ? 'admin.dashboard' : 'dashboard';
        $this->redirectIntended(default: route($defaultRoute, absolute: false), navigate: true);
    }

    public function resend(): void
    {
        $user = User::find(session('two_factor_user_id'));
        if (!$user || !$user->two_factor_enabled) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $code = $user->generateEmailOtp();
        app(BrevoMailService::class)->sendOtpEmail($user->email, $user->name, $code);
        session()->flash('status', 'A new security code has been sent.');
    }
};
?>

<div class="max-w-md mx-auto text-center">
    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Security check</h2>
    <p class="mt-3 text-sm text-slate-500">Enter the six-digit code sent to your email to finish signing in.</p>
    <form wire:submit="verify" class="mt-8 bg-white p-8 shadow-2xl rounded-3xl border border-slate-100 text-left space-y-5">
        <label for="otp" class="block text-sm font-bold text-slate-700">Email security code</label>
        <input wire:model="otp" id="otp" class="form-control text-center tracking-[.45em] text-xl" inputmode="numeric" maxlength="6" autocomplete="one-time-code" required>
        <x-input-error :messages="$errors->get('otp')" class="text-red-500 text-xs font-medium" />
        <button type="submit" class="btn-auth w-full">Verify and continue</button>
        <button type="button" wire:click="resend" class="w-full text-sm font-bold text-[rgb(0,127,127)]">Send a new code</button>
        @if(session('status'))<p class="text-sm text-emerald-700">{{ session('status') }}</p>@endif
    </form>
</div>
