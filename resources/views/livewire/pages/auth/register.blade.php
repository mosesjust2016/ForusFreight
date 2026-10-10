<?php

use App\Models\PhoneCountry;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use App\Services\BrevoMailService;
use App\Services\SmsService;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone_country = '260';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agree_terms = false;

    public function register(): void
    {
        $activeCountries = PhoneCountry::where('is_active', true)->get();
        $phoneRegex      = 'regex:/^\+?(' . $activeCountries->pluck('dial_code')->join('|') . ')\d{7,12}$/';
        $countryNames    = $activeCountries->map(fn($c) => "{$c->name} (+{$c->dial_code})")->join(', ');

        // The form has a show/hide toggle instead of a second "confirm" box,
        // so the confirmation always mirrors what was typed.
        $this->password_confirmation = $this->password;

        // Combine country code + local number into full phone BEFORE validation
        $fullPhone = '+' . $this->phone_country . ltrim($this->phone, '0');

        // Temporarily set phone to the combined number for validation
        $originalPhone = $this->phone;
        $this->phone = $fullPhone;

        try {
            $validated = $this->validate([
                'name'        => ['required', 'string', 'max:255'],
                'email'       => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
                'phone'       => ['required', 'string', 'max:20', $phoneRegex],
                'password'    => ['required', 'string', 'confirmed', Rules\Password::defaults()],
                'agree_terms' => ['accepted'],
            ], [
                'agree_terms.accepted' => 'You must accept the Terms & Conditions to create an account.',
                'phone.regex'          => "Only mobile numbers from the following countries are accepted: {$countryNames}.",
            ]);
        } finally {
            // Always restore the local number — including when validation
            // fails — or the field would show "+26012" and the next submit
            // would prefix the country code a second time.
            $this->phone = $originalPhone;
        }

        // Ensure the stored phone has the full prefix
        $validated['phone'] = $fullPhone;

        // Note: Password is NOT hashed here — the 'hashed' cast on the User model
        // handles hashing automatically when the attribute is set.

        // Clients whose shipments were added before they had an account exist
        // as placeholder users with no email/phone. If one matches this sign-up
        // (by the phone stored on their shipments, or by an exact name), claim
        // it in place instead of creating a duplicate — so the account comes
        // connected to their existing shipments.
        $placeholder = User::findShipmentPlaceholder($validated['name'], $validated['phone']);
        $linkedExistingShipments = false;

        try {
            if ($placeholder) {
                $placeholder->update([
                    'name'         => $validated['name'],
                    'email'        => $validated['email'],
                    'phone'        => $validated['phone'],
                    'password'     => $validated['password'],
                    'is_temporary' => false,
                ]);

                $user = $placeholder->refresh();
                $linkedExistingShipments = true;
            } else {
                $user = User::create(Arr::except($validated, ['agree_terms']));
            }
        } catch (\Throwable $e) {
            Log::error('Registration failed: could not create user', [
                'email' => $validated['email'] ?? null,
                'error' => $e->getMessage(),
            ]);
            $this->addError('form', 'Registration failed. Please try again.');
            return;
        }

        // Generate & send email OTP via Brevo
        $emailOtpSent = false;
        try {
            $emailOtp = $user->generateEmailOtp();
            $emailOtpSent = app(BrevoMailService::class)->sendOtpEmail($user->email, $user->name, $emailOtp);
        } catch (\Throwable $e) {
            Log::error('Registration: failed to send email OTP', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
        }

        // Generate & send phone OTP via SMS
        $phoneOtpSent = false;
        try {
            $phoneOtp = $user->generatePhoneOtp();
            $phoneOtpSent = app(SmsService::class)->sendOtp($user->phone, $phoneOtp);
        } catch (\Throwable $e) {
            Log::error('Registration: failed to send phone OTP', [
                'user_id' => $user->id,
                'phone'   => $user->phone,
                'error'   => $e->getMessage(),
            ]);
        }

        // Auto-login so they can access verification pages
        Auth::login($user);

        // Warn the user if OTPs could not be delivered
        $linkedNote = $linkedExistingShipments
            ? 'We found your existing shipment(s) and connected them to this account. '
            : '';

        if (!$emailOtpSent && !$phoneOtpSent) {
            session()->flash('warning', $linkedNote . 'Account created, but we could not send verification codes to your email or phone. Please contact support.');
        } elseif (!$emailOtpSent) {
            session()->flash('warning', $linkedNote . 'Account created, but we could not send the email verification code. Please check your phone for the SMS code, or contact support.');
        } elseif (!$phoneOtpSent) {
            session()->flash('warning', $linkedNote . 'Account created, but we could not send the SMS verification code. Please check your email for the verification code, or contact support.');
        } else {
            session()->flash('success', $linkedNote . 'Account created! Please verify your email and phone number.');
        }

        $this->redirect(route('verification.notice'), navigate: true);
    }
}; 
?>

<div class="rg">
    <style>
        .rg { max-width: 420px; margin: 0 auto; }

        .rg-head h2 {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.1;
            margin: 0;
        }

        .rg-head p {
            margin: 0.6rem 0 0;
            color: var(--ink-2);
            font-size: 0.98rem;
            line-height: 1.5;
        }

        .rg-progress {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0 2rem;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .rg-progress-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            width: 96px;
            flex-shrink: 0;
        }

        .rg-progress-bar span {
            height: 4px;
            border-radius: 4px;
            background: #d9ddda;
        }

        .rg-progress-bar span:first-child {
            background: var(--secondary);
        }

        .rg-progress strong {
            color: var(--ink);
            font-weight: 600;
        }

        .rg-form {
            display: grid;
            gap: 1.15rem;
        }

        .rg-field label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--ink);
        }

        .rg-hint {
            margin: 0.35rem 0 0;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .rg-pw {
            position: relative;
        }

        .rg-check {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin: 0.45rem 0 0;
            font-size: 0.8rem;
            color: var(--muted);
            transition: color 0.15s ease;
        }

        .rg-check i {
            font-size: 0.75rem;
            width: 14px;
        }

        .rg-check.is-met {
            color: #0f766e;
        }

        .rg-field label.rg-terms {
            margin: 0;
            font-weight: 400;
        }

        .rg-terms {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            font-size: 0.85rem;
            line-height: 1.45;
            color: var(--ink-2);
        }

        .rg-terms input {
            width: 18px;
            height: 18px;
            margin-top: 0.1rem;
            flex-shrink: 0;
            border-radius: 5px;
            border: 1.5px solid #aab4b1;
            color: var(--primary);
            cursor: pointer;
        }

        .rg-terms a {
            color: var(--teal-deep);
            font-weight: 600;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .rg .form-control.is-invalid,
        .rg .phone-input-group.is-invalid {
            border-color: #dc2626 !important;
        }

        /* ---- Country code picker ---- */
        [x-cloak] { display: none !important; }

        .cc-group {
            position: relative;
            display: flex;
            align-items: stretch;
            min-height: 48px;
            background: #fff;
            border: 1.5px solid #cfd5d3;
            border-radius: 10px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .cc-group:hover { border-color: #aab4b1; }

        .cc-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 127, 127, 0.16);
        }

        .rg .cc-group.is-invalid { border-color: #dc2626; }

        .cc-trigger {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0 0.75rem 0 0.85rem;
            border: none;
            border-right: 1.5px solid #e3e6e4;
            border-radius: 9px 0 0 9px;
            background: transparent;
            color: var(--ink);
            font: inherit;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .cc-trigger:hover,
        .cc-trigger[aria-expanded="true"] {
            background: #f4f5f2;
        }

        .cc-trigger:focus-visible {
            outline: none;
            background: #eef6f5;
        }

        .cc-flag {
            width: 1.4rem !important;
            height: 1.05rem;
            border-radius: 3px;
            flex-shrink: 0;
            background-size: cover;
            box-shadow: 0 0 0 1px rgba(13, 27, 32, 0.12);
        }

        .cc-code {
            font-variant-numeric: tabular-nums;
        }

        .cc-chevron {
            font-size: 0.65rem;
            color: var(--muted);
            transition: transform 0.2s ease;
        }

        .cc-chevron.is-open { transform: rotate(180deg); }

        .cc-input {
            flex: 1;
            min-width: 0;
            border: none !important;
            background: transparent;
            padding: 0.7rem 0.95rem !important;
            font-size: 1rem;
            color: var(--ink);
            border-radius: 0 10px 10px 0;
        }

        .cc-input:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        .cc-input::placeholder { color: #8a979c; }

        .cc-list {
            position: absolute;
            z-index: 30;
            top: calc(100% + 6px);
            left: -1.5px;
            width: min(300px, calc(100% + 3px));
            margin: 0;
            padding: 0.35rem;
            list-style: none;
            background: #fff;
            border: 1px solid rgba(13, 27, 32, 0.1);
            border-radius: 12px;
            box-shadow: 0 18px 40px rgba(13, 27, 32, 0.16), 0 2px 6px rgba(13, 27, 32, 0.06);
            outline: none;
        }

        .cc-option {
            display: grid;
            grid-template-columns: auto 1fr auto 1rem;
            align-items: center;
            gap: 0.7rem;
            padding: 0.65rem 0.7rem;
            border-radius: 8px;
            font-size: 0.93rem;
            color: var(--ink);
            cursor: pointer;
        }

        .cc-option.is-active { background: #f2f5f3; }

        .cc-option.is-selected .cc-name { font-weight: 600; }

        .cc-dial {
            color: var(--muted);
            font-variant-numeric: tabular-nums;
        }

        .cc-tick {
            font-size: 0.75rem;
            color: var(--primary);
            visibility: hidden;
        }

        .cc-option.is-selected .cc-tick { visibility: visible; }

        .cc-enter { transition: opacity 0.14s ease, transform 0.14s ease; }
        .cc-leave { transition: opacity 0.1s ease, transform 0.1s ease; }
        .cc-enter-start { opacity: 0; transform: translateY(-4px) scale(0.98); }
        .cc-enter-end { opacity: 1; transform: none; }

        @media (prefers-reduced-motion: reduce) {
            .cc-enter, .cc-leave, .cc-chevron { transition: none; }
        }

        .rg-error {
            margin: 0.4rem 0 0;
            font-size: 0.8rem;
            font-weight: 500;
            color: #b91c1c;
        }

        .rg-error ul { list-style: none; margin: 0; padding: 0; }

        .rg-alert {
            display: flex;
            gap: 0.6rem;
            align-items: flex-start;
            margin-bottom: 1.25rem;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .rg-alert--ok { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .rg-alert--warn { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .rg-alert--err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        .rg-submit { margin-top: 0.35rem; }

        .rg-foot {
            margin-top: 1.25rem;
            font-size: 0.875rem;
            color: var(--ink-2);
            text-align: center;
        }

        .rg-foot a {
            color: var(--teal-deep);
            font-weight: 700;
            text-decoration: none;
        }

        .rg-foot a:hover { text-decoration: underline; }

        @media (max-width: 600px) {
            .rg-head h2 { font-size: 1.7rem; }
            .rg-progress { margin: 1.1rem 0 1.5rem; }
        }
    </style>

    <div class="rg-head">
        <h2>Create your free account</h2>
        <p>Get your personal China warehouse address and track every shipment to Lusaka.</p>
    </div>

    <div class="rg-progress" aria-label="Step 1 of 3">
        <div class="rg-progress-bar" aria-hidden="true"><span></span><span></span><span></span></div>
        <span><strong>Step 1 of 3</strong> · Then verify your email and phone</span>
    </div>

    @if (session()->has('success'))
        <div class="rg-alert rg-alert--ok" role="status"><i class="fas fa-check-circle" aria-hidden="true"></i><span>{{ session('success') }}</span></div>
    @endif
    @if (session()->has('warning'))
        <div class="rg-alert rg-alert--warn" role="alert"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i><span>{{ session('warning') }}</span></div>
    @endif
    @if ($errors->has('form'))
        <div class="rg-alert rg-alert--err" role="alert"><i class="fas fa-exclamation-circle" aria-hidden="true"></i><span>{{ $errors->first('form') }}</span></div>
    @endif

    <form wire:submit="register" class="rg-form" novalidate>
        <div class="rg-field">
            <label for="name">Full name</label>
            <input wire:model="name" id="name" class="form-control @error('name') is-invalid @enderror" @error('name') aria-invalid="true" @enderror type="text" name="name" required autofocus autocomplete="name" aria-describedby="name-hint">
            <p class="rg-hint" id="name-hint">As on your ID — it goes on your parcel labels.</p>
            @error('name')<p class="rg-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="rg-field">
            <label for="email">Email</label>
            <input wire:model="email" id="email" class="form-control @error('email') is-invalid @enderror" @error('email') aria-invalid="true" @enderror type="email" name="email" required autocomplete="email" placeholder="you@example.com">
            @error('email')<p class="rg-error" role="alert">{{ $message }}</p>@enderror
        </div>

        @php
            // Turn the stored emoji flag (🇿🇲) into its ISO code (zm) for the SVG flag set,
            // so flags render everywhere — Windows shows emoji flags as plain letters.
            $countries = \App\Models\PhoneCountry::where('is_active', true)->get()->map(function ($c) {
                $iso = collect(mb_str_split((string) $c->flag))
                    ->map(fn ($ch) => mb_ord($ch) >= 0x1F1E6 && mb_ord($ch) <= 0x1F1FF ? chr(mb_ord($ch) - 0x1F1E6 + 97) : '')
                    ->implode('');
                return ['code' => (string) $c->dial_code, 'name' => $c->name, 'iso' => strlen($iso) === 2 ? $iso : null];
            })->values();
        @endphp
        <div class="rg-field">
            <label for="phone">Mobile number</label>
            <div class="cc-group @error('phone') is-invalid @enderror"
                x-data="{
                    open: false,
                    active: 0,
                    value: $wire.entangle('phone_country'),
                    options: @js($countries),
                    get current() { return this.options.find(o => o.code === String(this.value)) || this.options[0]; },
                    openList() {
                        this.open = true;
                        this.active = Math.max(0, this.options.indexOf(this.current));
                        this.$nextTick(() => this.$refs.list.focus());
                    },
                    choose(o) {
                        this.value = o.code;
                        this.open = false;
                        this.$nextTick(() => this.$refs.number.focus());
                    },
                    close() { this.open = false; this.$refs.trigger.focus(); },
                    move(step) { this.active = (this.active + step + this.options.length) % this.options.length; },
                }"
                @keydown.escape.window="open && close()">
                <button type="button" class="cc-trigger" x-ref="trigger" wire:ignore
                    @click="open ? (open = false) : openList()"
                    @keydown.arrow-down.prevent="openList()"
                    aria-haspopup="listbox" :aria-expanded="open.toString()" aria-expanded="false"
                    :aria-label="'Country code: ' + current.name + ' +' + current.code">
                    <span class="cc-flag fi" :class="current.iso ? 'fi-' + current.iso : ''" aria-hidden="true"></span>
                    <span class="cc-code" x-text="'+' + current.code">+260</span>
                    <i class="fas fa-chevron-down cc-chevron" :class="open && 'is-open'" aria-hidden="true"></i>
                </button>

                <input wire:model="phone" id="phone" x-ref="number" class="cc-input" type="tel" name="phone" required
                    autocomplete="tel-national" inputmode="tel" placeholder="97 123 4567"
                    aria-describedby="phone-hint" @error('phone') aria-invalid="true" @enderror>

                <ul class="cc-list" role="listbox" x-ref="list" tabindex="-1" aria-label="Choose a country code" wire:ignore.self
                    x-show="open" x-cloak
                    x-transition:enter="cc-enter" x-transition:enter-start="cc-enter-start" x-transition:enter-end="cc-enter-end"
                    x-transition:leave="cc-leave" x-transition:leave-end="cc-enter-start"
                    @click.outside="open = false"
                    @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)"
                    @keydown.enter.prevent="choose(options[active])" @keydown.space.prevent="choose(options[active])"
                    @keydown.tab="open = false"
                    :aria-activedescendant="'cc-opt-' + active">
                    <template x-for="(o, i) in options" :key="o.code">
                        <li role="option" class="cc-option" :id="'cc-opt-' + i"
                            :aria-selected="(o.code === current.code).toString()"
                            :class="{ 'is-active': i === active, 'is-selected': o.code === current.code }"
                            @mouseenter="active = i" @click="choose(o)">
                            <span class="cc-flag fi" :class="o.iso ? 'fi-' + o.iso : ''" aria-hidden="true"></span>
                            <span class="cc-name" x-text="o.name"></span>
                            <span class="cc-dial" x-text="'+' + o.code"></span>
                            <i class="fas fa-check cc-tick" aria-hidden="true"></i>
                        </li>
                    </template>
                </ul>
            </div>
            <p class="rg-hint" id="phone-hint">Without the country code. We’ll text you a verification code.</p>
            @error('phone')<p class="rg-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="rg-field" x-data="{ show: false, len: 0 }">
            <label for="password">Password</label>
            <div class="rg-pw">
                <input wire:model="password" id="password" class="form-control has-toggle @error('password') is-invalid @enderror" :type="show ? 'text' : 'password'" type="password" name="password" required autocomplete="new-password" @input="len = $event.target.value.length" aria-describedby="password-rule">
                <button type="button" class="pw-toggle" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'" aria-label="Show password">
                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </button>
            </div>
            <p class="rg-check" id="password-rule" :class="{ 'is-met': len >= 8 }">
                <i class="fas" :class="len >= 8 ? 'fa-circle-check' : 'fa-circle'" aria-hidden="true"></i>
                At least 8 characters
            </p>
            @error('password')<p class="rg-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="rg-field">
            <label class="rg-terms" for="agree_terms">
                <input wire:model="agree_terms" type="checkbox" id="agree_terms">
                <span>I agree to the <a href="{{ route('terms') }}" target="_blank" rel="noopener">Terms</a> and <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Privacy Policy</a>.</span>
            </label>
            @error('agree_terms')<p class="rg-error" role="alert">{{ $message }}</p>@enderror
        </div>

        <div class="rg-submit">
            <button type="submit" class="btn-auth" wire:loading.attr="disabled" wire:target="register">
                <span wire:loading.remove wire:target="register">Create free account</span>
                <span wire:loading wire:target="register"><i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> Creating your account…</span>
            </button>
        </div>
    </form>

    <p class="rg-foot">Already have an account? <a href="{{ route('login') }}" wire:navigate>Sign in</a></p>
</div>
