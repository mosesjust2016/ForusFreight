<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Reset Password</h2>
        <p class="mt-3 text-sm text-slate-500">
            Enter your email to receive a reset link
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="bg-white p-8 shadow-2xl shadow-slate-200/50 rounded-3xl border border-slate-100 transition-all duration-300">
        <form wire:submit="sendPasswordResetLink" class="space-y-6">
            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input wire:model="email" id="email"
                        class="form-control has-icon"
                        type="email" name="email" required autofocus placeholder="name@company.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                    <span wire:loading.remove wire:target="sendPasswordResetLink">Send Reset Link</span>
                    <span wire:loading wire:target="sendPasswordResetLink" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Sending...
                    </span>
                    <i wire:loading.remove wire:target="sendPasswordResetLink" class="fas fa-paper-plane text-xs opacity-50 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </form>

        <div class="mt-8 pt-8 border-t border-slate-100 text-center">
            <a href="{{ route('login') }}" class="text-sm font-bold text-[rgb(0,127,127)] hover:text-[rgb(255,98,0)] transition-colors inline-flex items-center gap-2" wire:navigate>
                <i class="fas fa-arrow-left text-xs"></i>
                Back to login
            </a>
        </div>
    </div>
</div>
