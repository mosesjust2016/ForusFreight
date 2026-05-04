<?php

use App\Livewire\Forms\LoginForm;
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

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; 
?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">Sign in to your account</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Or
            <a href="{{ route('register') }}" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors duration-200" wire:navigate>
                create a new account
            </a>
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-gray-100 dark:border-gray-700 transition-all duration-300">
        <form wire:submit="login" class="space-y-6">
            <!-- Email Address -->
            <div>
                <x-input-label for="email" value="Email address" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                <div class="mt-2">
                    <x-text-input wire:model="form.email" id="email" 
                        class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900/50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 transition-all duration-300 ease-in-out" 
                        type="email" name="email" required autofocus autocomplete="username" placeholder="you@company.com" />
                </div>
                <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-red-500 text-sm font-medium" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" value="Password" class="text-sm font-semibold text-gray-700 dark:text-gray-300" />
                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors duration-200" wire:navigate>
                                Forgot password?
                            </a>
                        </div>
                    @endif
                </div>
                <div class="mt-2">
                    <x-text-input wire:model="form.password" id="password" 
                        class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-900/50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 transition-all duration-300 ease-in-out"
                        type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                </div>
                <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-red-500 text-sm font-medium" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-900 transition duration-150 ease-in-out cursor-pointer" name="remember">
                <label for="remember" class="ml-3 block text-sm font-medium leading-6 text-gray-700 dark:text-gray-300 cursor-pointer">
                    Remember me
                </label>
            </div>

            <!-- Actions -->
            <div class="pt-2">
                <button type="submit" class="group flex w-full justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-emerald-600 px-3 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:from-indigo-500 hover:to-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transform transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50">
                    <span wire:loading.remove wire:target="login">Sign in</span>
                    <span wire:loading wire:target="login" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Optimizing Logistics...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
