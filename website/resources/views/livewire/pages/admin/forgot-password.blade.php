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
    <h2 style="color: var(--primary-green);">Admin Password Recovery</h2>
    <p class="subtitle" style="margin-bottom: 1.5rem;">
        Enter your administrative email to receive a reset link.
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="auth-form">
        <!-- Email Address -->
        <div>
            <input wire:model="email" id="email" 
                   type="email" name="email" 
                   placeholder="Admin Email Address"
                   class="auth-input"
                   required autofocus />
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-btn-primary" style="background: var(--primary-green);">
            <span wire:loading.remove wire:target="sendPasswordResetLink">Send Recovery Link</span>
            <span wire:loading wire:target="sendPasswordResetLink">Sending...</span>
        </button>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="{{ route('admin.login') }}" wire:navigate style="color: var(--text-gray); font-size: 0.85rem; text-decoration: none; font-weight: 600;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </form>
</div>
