<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Fire email verification notification
        event(new Registered($user));

        // Do NOT auto-login until verified
        session()->flash('success', 'Registration successful! Please check your email to verify your account.');
        $this->redirect(route('login'), navigate: true);
    }
}; 
?>

<div class="max-w-md mx-auto bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6 text-center">Create an Account</h2>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-center">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="register" class="space-y-4">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-green-600 dark:bg-gray-700 dark:text-gray-100" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-green-600 dark:bg-gray-700 dark:text-gray-100" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-green-600 dark:bg-gray-700 dark:text-gray-100" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-green-600 dark:bg-gray-700 dark:text-gray-100" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('login') }}" class="text-sm font-medium text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                {{ __('Already registered? Log in') }}
            </a>

            <x-primary-button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>

<style>
input:focus, select:focus, textarea:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.5); /* green focus */
}
</style>
