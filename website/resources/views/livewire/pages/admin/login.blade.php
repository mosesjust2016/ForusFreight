<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        $user = Auth::user();

        // Allow super-admins and any user with a staff role
        if (!$user->is_admin && !$user->hasAnyRole(['admin_staff', 'sales'])) {
            Auth::logout();
            $this->addError('form.email', 'This area is restricted to authorized staff only.');
            return;
        }

        if (! $user->hasVerifiedEmail()) {
            $user->generateEmailOtp();
            $this->redirect(route('verification.notice'), navigate: true);
            return;
        }

        if (! $user->hasVerifiedPhone()) {
            $user->generatePhoneOtp();
            $this->redirect(route('verification.phone'), navigate: true);
            return;
        }

        $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
    }
}; 
?>

<div>
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wider mb-4 border border-slate-200">
            <i class="fas fa-lock text-[rgb(255,98,0)]"></i>
            Management Access
        </div>
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Admin Portal</h2>
        <p class="mt-3 text-sm text-slate-500">
            Authorized personnel only
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="bg-white p-8 shadow-2xl shadow-slate-200/50 rounded-3xl border border-slate-100 transition-all duration-300">
        <form wire:submit="login" class="space-y-6">
            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Admin Email</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-user-shield"></i></span>
                    <input wire:model="form.email" id="email"
                        class="form-control has-icon"
                        type="email" name="email" required autofocus autocomplete="username" placeholder="admin@forusfl.co.zm" />
                </div>
                <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                    @if (Route::has('admin.password.request'))
                        <a href="{{ route('admin.password.request') }}" class="text-xs font-bold text-[rgb(0,127,127)] hover:text-[rgb(255,98,0)] transition-colors duration-200" wire:navigate>
                            Forgot?
                        </a>
                    @endif
                </div>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-key"></i></span>
                    <input wire:model="form.password" id="password"
                        class="form-control has-icon"
                        type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <!-- Actions -->
            <div class="pt-2">
                <button type="submit" class="btn-auth flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="login">Login to Dashboard</span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Verifying...
                    </span>
                    <i wire:loading.remove wire:target="login" class="fas fa-shield-check text-xs opacity-50 group-hover:scale-110 transition-transform"></i>
                </button>
            </div>
        </form>

        <div class="mt-8 pt-8 border-t border-slate-100 flex flex-col gap-4">
            <a href="{{ url('/') }}" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-2 border-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-50 hover:border-slate-200 transition-all">
                <i class="fas fa-arrow-left text-xs"></i>
                Back to Main Website
            </a>
            
            <p class="text-center text-[10px] text-slate-400 font-medium uppercase tracking-widest">
                All access is monitored and logged
            </p>
        </div>
    </div>
</div>
