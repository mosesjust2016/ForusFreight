

<div>
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Activate Your Account</h2>
        <p class="mt-3 text-sm text-slate-500">
            @if($step === 'phone')
                Verify your phone number to continue
            @elseif($step === 'email')
                Provide your email to complete activation
            @else
                Verify your email to activate your account
            @endif
        </p>
    </div>

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-sm font-medium text-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('phone_success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium text-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('phone_success') }}
        </div>
    @endif

    @if (session()->has('email_sent'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-sm font-medium text-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('email_sent') }}
        </div>
    @endif

    <div class="bg-white p-8 shadow-2xl shadow-slate-200/50 rounded-3xl border border-slate-100">
        
        @if($step === 'enter_phone')
            <form wire:submit="lookupUser" class="space-y-6">
                <div>
                    <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-phone"></i></span>
                        <input wire:model="phone" id="phone"
                            class="form-control has-icon"
                            type="tel" required autocomplete="tel" placeholder="260970000000" />
                    </div>
                    <x-input-error :messages="$errors->get('phone')" class="mt-2 text-red-500 text-xs font-medium" />
                </div>

                <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                    <span wire:loading.remove wire:target="lookupUser">Continue</span>
                    <span wire:loading wire:target="lookupUser" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Looking up...
                    </span>
                    <i wire:loading.remove wire:target="lookupUser" class="fas fa-arrow-right text-xs opacity-50 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

        @elseif($step === 'phone')
            <form wire:submit="verifyPhone" class="space-y-6">
                <div>
                    <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                    <input wire:model="phone" id="phone"
                        class="form-control"
                        type="tel" readonly />
                </div>

                <div>
                    <label for="otp" class="block text-sm font-bold text-slate-700 mb-2">Verification Code</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <input wire:model="otp" id="otp"
                            class="form-control pl-11"
                            type="text" name="otp" required
                            maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                            placeholder="000000"
                            style="letter-spacing: 0.5rem; font-size: 1.25rem; text-align: center;" />
                    </div>
                    <x-input-error :messages="$errors->get('otp')" class="mt-2 text-red-500 text-xs font-medium" />
                </div>

                <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                    <span wire:loading.remove wire:target="verifyPhone">Verify Phone</span>
                    <span wire:loading wire:target="verifyPhone" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Verifying...
                    </span>
                    <i wire:loading.remove wire:target="verifyPhone" class="fas fa-check text-xs opacity-50 group-hover:scale-110 transition-transform"></i>
                </button>
            </form>

            <div class="mt-6 text-center">
                <button wire:click="resendPhoneOtp" 
                        class="text-sm font-bold text-[rgb(0,127,127)] hover:text-[rgb(255,98,0)] transition-colors">
                    <span wire:loading.remove wire:target="resendPhoneOtp">Resend Code</span>
                    <span wire:loading wire:target="resendPhoneOtp">Sending...</span>
                </button>
            </div>

        @elseif($step === 'email')
            <form wire:submit="submitEmail" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input wire:model="email" id="email"
                            class="form-control has-icon"
                            type="email" required autocomplete="email" placeholder="your@email.com" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-medium" />
                </div>

                <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                    <span wire:loading.remove wire:target="submitEmail">Continue</span>
                    <span wire:loading wire:target="submitEmail" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Sending...
                    </span>
                    <i wire:loading.remove wire:target="submitEmail" class="fas fa-arrow-right text-xs opacity-50 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

        @elseif($step === 'verify_email')
            <form wire:submit="verifyEmail" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <input id="email_display" class="form-control" type="email" value="{{ $user->email }}" readonly />
                </div>

                <div>
                    <label for="email_otp" class="block text-sm font-bold text-slate-700 mb-2">Verification Code</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <input wire:model="email_otp" id="email_otp"
                            class="form-control pl-11"
                            type="text" name="email_otp" required
                            maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                            placeholder="000000"
                            style="letter-spacing: 0.5rem; font-size: 1.25rem; text-align: center;" />
                    </div>
                    <x-input-error :messages="$errors->get('email_otp')" class="mt-2 text-red-500 text-xs font-medium" />
                </div>

                <button type="submit" class="btn-auth flex items-center justify-center gap-2 group">
                    <span wire:loading.remove wire:target="verifyEmail">Activate Account</span>
                    <span wire:loading wire:target="verifyEmail" class="flex items-center gap-2">
                        <i class="fas fa-circle-notch animate-spin"></i>
                        Activating...
                    </span>
                    <i wire:loading.remove wire:target="verifyEmail" class="fas fa-check text-xs opacity-50 group-hover:scale-110 transition-transform"></i>
                </button>
            </form>

            <div class="mt-6 text-center">
                <button wire:click="resendEmailOtp" 
                        class="text-sm font-bold text-[rgb(0,127,127)] hover:text-[rgb(255,98,0)] transition-colors">
                    <span wire:loading.remove wire:target="resendEmailOtp">Resend Code</span>
                    <span wire:loading wire:target="resendEmailOtp">Sending...</span>
                </button>
            </div>
        @endif

    </div>
</div>
