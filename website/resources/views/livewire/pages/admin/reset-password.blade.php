<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->string('email');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            return;
        }

        Session::flash('status', __($status));
        $this->redirectRoute('admin.login', navigate: true);
    }
}; ?>

<div>
    <h2 style="color: var(--primary-green);">Set New Admin Password</h2>
    <p class="subtitle" style="margin-bottom: 1.5rem;">
        Secure your administrative account with a new password.
    </p>

    <form wire:submit="resetPassword" class="auth-form">
        <!-- Email Address -->
        <div>
            <input wire:model="email" id="email" 
                   type="email" name="email" 
                   class="auth-input"
                   placeholder="Admin Email"
                   required autofocus autocomplete="username" />
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <input wire:model="password" id="password" 
                   type="password" name="password" 
                   class="auth-input"
                   placeholder="New Password"
                   required autocomplete="new-password" />
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <input wire:model="password_confirmation" id="password_confirmation" 
                   type="password" name="password_confirmation" 
                   class="auth-input"
                   placeholder="Confirm New Password"
                   required autocomplete="new-password" />
            @error('password_confirmation')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-btn-primary" style="background: var(--primary-green);">
            <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
            <span wire:loading wire:target="resetPassword">Updating...</span>
        </button>
    </form>
</div>
