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

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
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

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Set New Password</h2>
        <p class="mt-3 text-sm text-slate-500">
            Secure your account with a new password
        </p>
    </div>

    <div class="bg-white p-8 shadow-2xl shadow-slate-200/50 rounded-3xl border border-slate-100 transition-all duration-300">
        <form wire:submit="resetPassword" class="space-y-6">
            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                <div class="input-wrapper">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input wire:model="email" id="email"
                        class="form-control has-icon"
                        type="email" name="email" required autofocus autocomplete="username" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2">New Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input wire:model="password" id="password"
                            class="form-control has-icon"
                            type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs font-medium" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Confirm Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-shield-check"></i></span>
                        <input wire:model="password_confirmation" id="password_confirmation"
                            class="form-control has-icon"
                            type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-xs font-medium" />
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                    <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                    <span wire:loading wire:target="resetPassword" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Updating...
                    </span>
                    <i wire:loading.remove wire:target="resetPassword" class="fas fa-key text-xs opacity-50 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>
</div>
