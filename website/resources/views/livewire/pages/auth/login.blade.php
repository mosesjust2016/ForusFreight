<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();
        $this->form->authenticate();
        Session::regenerate();

        $user = Auth::user();

        // Check email verification
        if (! $user->hasVerifiedEmail()) {
            $user->generateEmailOtp();
            $this->redirect(route('verification.notice'), navigate: true);
            return;
        }

        // Check phone verification
        if (! $user->hasVerifiedPhone()) {
            $user->generatePhoneOtp();
            $this->redirect(route('verification.phone'), navigate: true);
            return;
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; 
?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Sign in</h2>
        <p class="mt-3 text-sm text-slate-500">
            Securely manage your global shipments
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="bg-white p-8 shadow-2xl shadow-slate-200/50 rounded-3xl border border-slate-100 transition-all duration-300">
        <form wire:submit="login" class="space-y-6">
            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input wire:model="form.email" id="email"
                        class="form-control has-icon"
                        type="email" name="email" required autofocus autocomplete="username" placeholder="name@company.com" />
                </div>
                <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-[rgb(0,127,127)] hover:text-[rgb(255,98,0)] transition-colors duration-200" wire:navigate>
                            Forgot?
                        </a>
                    @endif
                </div>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input wire:model="form.password" id="password"
                        class="form-control has-icon"
                        type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[rgb(0,127,127)] focus:ring-[rgb(0,127,127)] transition duration-150 ease-in-out cursor-pointer" name="remember">
                    <span class="ml-2 text-sm font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Remember me</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="pt-2">
                <button type="submit" class="btn-auth flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="login">Sign In</span>
                    <span wire:loading wire:target="login" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Authenticating...
                    </span>
                    <i wire:loading.remove wire:target="login" class="fas fa-arrow-right text-xs opacity-50 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </form>

        <div class="mt-8 pt-8 border-t border-slate-100 text-center">
            <p class="text-sm text-slate-500">
                New to Forus Freight?
                <a href="{{ route('register') }}" class="font-bold text-[rgb(0,127,127)] hover:text-[rgb(255,98,0)] transition-colors" wire:navigate>
                    Create an account
                </a>
            </p>
        </div>
    </div>
</div>
