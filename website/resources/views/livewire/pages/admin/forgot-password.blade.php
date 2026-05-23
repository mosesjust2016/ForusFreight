<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // Check if user is admin
        $user = \App\Models\User::where('email', $this->email)->first();
        if (!$user || !$user->is_admin) {
            $this->addError('email', 'Unauthorized access for this email.');
            return;
        }

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
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wider mb-4 border border-slate-200">
            <i class="fas fa-shield-alt text-[rgb(255,98,0)]"></i>
            Management Recovery
        </div>
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Admin Recovery</h2>
        <p class="mt-3 text-sm text-slate-500">
            Request an administrative password reset
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="bg-white p-8 shadow-2xl shadow-slate-200/50 rounded-3xl border border-slate-100 transition-all duration-300">
        <form wire:submit="sendPasswordResetLink" class="space-y-6">
            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Admin Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-envelope-open-text"></i></span>
                    <input wire:model="email" id="email"
                        class="form-control has-icon"
                        type="email" name="email" required autofocus placeholder="admin@forusfl.co.zm" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                    <span wire:loading.remove wire:target="sendPasswordResetLink">Send Recovery Link</span>
                    <span wire:loading wire:target="sendPasswordResetLink" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Processing...
                    </span>
                    <i wire:loading.remove wire:target="sendPasswordResetLink" class="fas fa-paper-plane text-xs opacity-50 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </form>

        <div class="mt-8 pt-8 border-t border-slate-100 text-center">
            <a href="{{ route('admin.login') }}" class="text-sm font-bold text-[rgb(0,127,127)] hover:text-[rgb(255,98,0)] transition-colors inline-flex items-center gap-2" wire:navigate>
                <i class="fas fa-arrow-left text-xs"></i>
                Back to Admin Login
            </a>
        </div>
    </div>
</div>
