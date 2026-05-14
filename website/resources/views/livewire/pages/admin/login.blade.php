<?php

use App\Livewire\Forms\LoginForm;
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

        if (!Auth::user()->is_admin) {
            Auth::logout();
            $this->addError('form.email', 'This area is restricted to administrators only.');
            return;
        }

        $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
    }
}; 
?>

<div>
    <h2 style="color: var(--primary-green);">Admin Portal</h2>
    <p class="subtitle">
        Secure Management Access
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="auth-form">
        <!-- Email -->
        <div>
            <input wire:model="form.email"
                   id="email" type="email" name="email"
                   placeholder="Admin Email"
                   class="auth-input"
                   required autofocus autocomplete="username" />
            @error('form.email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <input wire:model="form.password"
                   id="password" type="password" name="password"
                   placeholder="Password"
                   class="auth-input"
                   required autocomplete="current-password" />
            @error('form.password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
            @if (Route::has('admin.password.request'))
                <div class="auth-link-right">
                    <a href="{{ route('admin.password.request') }}" wire:navigate>Forgot password?</a>
                </div>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit" class="auth-btn-primary" style="background: #00706f; background: var(--primary-green); margin-bottom: 1rem; height: 50px; display: flex; align-items: center; justify-content: center;">
            <span wire:loading.remove>Login to Dashboard</span>
            <span wire:loading>Authenticating...</span>
        </button>

        <a href="{{ url('/') }}" class="auth-btn-secondary" style="display: flex; align-items: center; justify-content: center; text-decoration: none; border: 2px solid #e2e8f0; color: #64748b; padding: 0.85rem; border-radius: 12px; font-weight: 700; font-size: 0.9rem; transition: all 0.3s;">
            <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i> Back to Main Website
        </a>

        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.8rem; color: #64748b;">
            Authorized personnel only. All access is logged.
        </p>
    </form>
</div>
