<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use App\Services\SmsService;
use App\Services\BrevoMailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class ActivateAccount extends Component
{
    #[Url]
    public string $phone = '';

    public string $otp = '';
    public string $email = '';
    public string $email_otp = '';
    public ?User $user = null;
    public bool $phone_verified = false;
    public bool $resent = false;
    public string $step = 'phone';

    public function mount(): void
    {
        if ($this->phone) {
            $this->loadUser();
        } else {
            $this->step = 'enter_phone';
        }
    }

    public function lookupUser(): void
    {
        $this->validate(['phone' => ['required', 'string', 'max:20']]);
        $this->loadUser();
    }

    public function loadUser(): void
    {
        $this->user = User::where('phone', $this->phone)->first();

        if (!$this->user) {
            session()->flash('error', 'No account found with this phone number.');
            $this->redirect(route('login'), navigate: true);
            return;
        }

        if (!$this->user->isTemporary() && $this->user->isActive()) {
            session()->flash('info', 'Your account is already active. Please login.');
            $this->redirect(route('login'), navigate: true);
            return;
        }

        if ($this->user->hasVerifiedPhone()) {
            $this->phone_verified = true;
            $this->step = 'email';
        } else {
            $this->step = 'phone';
        }
    }

    public function verifyPhone(): void
    {
        $this->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if (!$this->user) {
            $this->loadUser();
        }

        if ($this->user->verifyPhoneOtp($this->otp)) {
            $this->phone_verified = true;
            $this->step = 'email';
            $this->otp = '';
            session()->flash('phone_success', 'Phone verified! Now please provide your email to activate your account.');
        } else {
            $this->addError('otp', 'The code is invalid or has expired. Please try again.');
        }
    }

    public function resendPhoneOtp(): void
    {
        if (!$this->user) {
            $this->loadUser();
        }

        try {
            $otp = $this->user->generatePhoneOtp();
            $sent = app(SmsService::class)->sendOtp($this->user->phone, $otp);

            if (!$sent) {
                $this->addError('otp', 'Failed to send SMS verification code. Please try again or contact support.');
                Log::error('ActivateAccount: SMS OTP send failed', ['user_id' => $this->user->id, 'phone' => $this->user->phone]);
                return;
            }
        } catch (\Throwable $e) {
            Log::error('ActivateAccount: exception sending SMS OTP', ['user_id' => $this->user->id, 'error' => $e->getMessage()]);
            $this->addError('otp', 'Failed to send SMS verification code. Please try again or contact support.');
            return;
        }

        $this->resent = true;
        $this->otp = '';
    }

    public function submitEmail(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
        ]);

        $this->user->update([
            'email' => $this->email,
        ]);

        try {
            $emailOtp = $this->user->generateEmailOtp();
            $sent = app(BrevoMailService::class)->sendOtpEmail($this->user->email, $this->user->name, $emailOtp);

            if (!$sent) {
                $this->addError('email', 'Failed to send email verification code. Please try again or contact support.');
                Log::error('ActivateAccount: email OTP send failed', ['user_id' => $this->user->id, 'email' => $this->user->email]);
                return;
            }
        } catch (\Throwable $e) {
            Log::error('ActivateAccount: exception sending email OTP', ['user_id' => $this->user->id, 'error' => $e->getMessage()]);
            $this->addError('email', 'Failed to send email verification code. Please try again or contact support.');
            return;
        }

        $this->step = 'verify_email';
        session()->flash('email_sent', 'Verification code sent to your email.');
    }

    public function verifyEmail(): void
    {
        $this->validate([
            'email_otp' => ['required', 'string', 'size:6'],
        ]);

        if ($this->user->verifyEmailOtp($this->email_otp)) {
            $this->user->activateAccount();

            Auth::login($this->user);

            session()->flash('success', 'Account activated successfully! You can now access your dashboard.');
            $this->redirect(route('dashboard'), navigate: true);
        } else {
            $this->addError('email_otp', 'The code is invalid or has expired. Please try again.');
        }
    }

    public function resendEmailOtp(): void
    {
        try {
            $otp = $this->user->generateEmailOtp();
            $sent = app(BrevoMailService::class)->sendOtpEmail($this->user->email, $this->user->name, $otp);

            if (!$sent) {
                $this->addError('email_otp', 'Failed to send email verification code. Please try again or contact support.');
                Log::error('ActivateAccount: email OTP resend failed', ['user_id' => $this->user->id, 'email' => $this->user->email]);
                return;
            }
        } catch (\Throwable $e) {
            Log::error('ActivateAccount: exception resending email OTP', ['user_id' => $this->user->id, 'error' => $e->getMessage()]);
            $this->addError('email_otp', 'Failed to send email verification code. Please try again or contact support.');
            return;
        }

        $this->resent = true;
        $this->email_otp = '';
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.pages.auth.activate-account');
    }
}
