<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use App\Mail\EmailVerificationOtp;
use App\Services\SmsService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Generate & send email OTP
        $emailOtp = $user->generateEmailOtp();
        Mail::to($user->email)->send(new EmailVerificationOtp($user, $emailOtp));

        // Generate & send phone OTP
        $phoneOtp = $user->generatePhoneOtp();
        app(SmsService::class)->sendOtp($user->phone, $phoneOtp);

        event(new Registered($user));

        // Auto-login so they can access verification pages
        Auth::login($user);

        session()->flash('success', 'Account created! Please verify your email and phone number.');
        $this->redirect(route('verification.notice'), navigate: true);
    }
}; 
?>

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Create Account</h2>
        <p class="mt-3 text-sm text-slate-500">
            Join Forus Freight global network
        </p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium text-center animate-fade-in">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-8 shadow-2xl shadow-slate-200/50 rounded-3xl border border-slate-100 transition-all duration-300">
        <form wire:submit="register" class="space-y-5">

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-user"></i>
                    </div>
                    <input wire:model="name" id="name"
                        class="form-control pl-11"
                        type="text" name="name" required autofocus autocomplete="name" placeholder="John Doe" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <input wire:model="email" id="email"
                        class="form-control pl-11"
                        type="email" name="email" required autocomplete="username" placeholder="john@example.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">Mobile Number</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-mobile-screen"></i>
                    </div>
                    <input wire:model="phone" id="phone"
                        class="form-control pl-11"
                        type="tel" name="phone" required autocomplete="tel" placeholder="+260 96 123 4567" />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-2 text-red-500 text-xs font-medium" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input wire:model="password" id="password"
                            class="form-control pl-11"
                            type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs font-medium" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">Confirm</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <input wire:model="password_confirmation" id="password_confirmation"
                            class="form-control pl-11"
                            type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-xs font-medium" />
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4">
                <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                    <span wire:loading.remove wire:target="register">Create Account</span>
                    <span wire:loading wire:target="register" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Setting up...
                    </span>
                    <i wire:loading.remove wire:target="register" class="fas fa-user-plus text-xs opacity-50 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>

        </form>

        <div class="mt-8 pt-8 border-top border-slate-100 text-center">
            <p class="text-sm text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-bold text-[rgb(0,127,127)] hover:text-[rgb(255,98,0)] transition-colors" wire:navigate>
                    Sign in here
                </a>
            </p>
        </div>
    </div>
</div>
