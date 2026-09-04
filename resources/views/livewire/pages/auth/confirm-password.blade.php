<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Security Check</h2>
        <p class="mt-3 text-sm text-slate-500">
            Please confirm your password to continue
        </p>
    </div>

    <div class="bg-white p-8 shadow-2xl shadow-slate-200/50 rounded-3xl border border-slate-100 transition-all duration-300">
        <p class="text-sm text-slate-600 mb-6">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>

        <form wire:submit="confirmPassword" class="space-y-6">
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input wire:model="password" id="password" 
                        class="form-control pl-11" 
                        type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                    <span wire:loading.remove wire:target="confirmPassword">{{ __('Confirm Password') }}</span>
                    <span wire:loading wire:target="confirmPassword" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Verifying...
                    </span>
                    <i wire:loading.remove wire:target="confirmPassword" class="fas fa-shield-check text-xs opacity-50 group-hover:scale-110 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>
</div>
