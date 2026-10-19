<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\BrevoMailService;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'address',
        'password',
        'is_admin',
        'is_temporary',
        'account_status',
        'phone_otp',
        'phone_otp_expires_at',
        'email_otp',
        'email_otp_expires_at',
        'crm_status',
        'assigned_agent',
        'lead_score',
        'last_engagement_at',
        'preferences',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'phone_otp_expires_at' => 'datetime',
        'email_otp_expires_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot('role', 'is_primary')
            ->withTimestamps();
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'contact_id');
    }

    public function assignedDeals(): HasMany
    {
        return $this->hasMany(Deal::class, 'assigned_to');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'contact_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'contact_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ContactNote::class, 'contact_id');
    }

    public function communicationLogs(): HasMany
    {
        return $this->hasMany(CommunicationLog::class);
    }

    /* ──────────────────────────────────────────────────────────
       RBAC / Roles
       ────────────────────────────────────────────────────────── */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole(string $roleName): bool
    {
        if ($this->is_admin && $roleName === 'admin') {
            return true;
        }
        return $this->roles->contains('name', $roleName);
    }

    public function hasAnyRole(array $roleNames): bool
    {
        foreach ($roleNames as $name) {
            if ($this->hasRole($name)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermission(string $permission): bool
    {
        // Super admin bypass
        if ($this->is_admin) {
            return true;
        }
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /* ──────────────────────────────────────────────────────────
       Phone verification helpers
       ────────────────────────────────────────────────────────── */

    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified_at !== null;
    }

    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
            'phone_otp' => null,
            'phone_otp_expires_at' => null,
        ])->save();
    }

    public function generatePhoneOtp(): string
    {
        $otp = (string) random_int(100000, 999999);

        $this->forceFill([
            'phone_otp' => $otp,
            'phone_otp_expires_at' => Carbon::now()->addMinutes(10),
        ])->save();

        return $otp;
    }

    public function verifyPhoneOtp(string $otp): bool
    {
        if (
            $this->phone_otp === $otp &&
            $this->phone_otp_expires_at &&
            Carbon::now()->lessThanOrEqualTo($this->phone_otp_expires_at)
        ) {
            $this->markPhoneAsVerified();
            return true;
        }

        return false;
    }

    /* ──────────────────────────────────────────────────────────
       Email OTP helpers
       ────────────────────────────────────────────────────────── */

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'email_otp' => null,
            'email_otp_expires_at' => null,
        ])->save();
    }

    public function generateEmailOtp(): string
    {
        $otp = (string) random_int(100000, 999999);

        $this->forceFill([
            'email_otp' => $otp,
            'email_otp_expires_at' => Carbon::now()->addMinutes(10),
        ])->save();

        return $otp;
    }

    public function verifyEmailOtp(string $otp): bool
    {
        if (
            $this->email_otp === $otp &&
            $this->email_otp_expires_at &&
            Carbon::now()->lessThanOrEqualTo($this->email_otp_expires_at)
        ) {
            $this->markEmailAsVerified();
            return true;
        }

        return false;
    }

    /* ──────────────────────────────────────────────────────────
       Overall verification
       ────────────────────────────────────────────────────────── */

    public function isFullyVerified(): bool
    {
        return $this->hasVerifiedEmail() && $this->hasVerifiedPhone();
    }

    public function isTemporary(): bool
    {
        return $this->is_temporary === true;
    }

    public function isActive(): bool
    {
        return $this->account_status === 'active';
    }

    public function activateAccount(): bool
    {
        return $this->forceFill([
            'account_status' => 'active',
            'is_temporary' => false,
        ])->save();
    }

    public static function findOrCreateByPhone(string $phone, string $name): self
    {
        $user = self::where('phone', $phone)->first();

        if ($user) {
            return $user;
        }

        return self::create([
            'name' => $name,
            'phone' => $phone,
            'email' => null,
            'password' => null,
            'is_temporary' => true,
            'account_status' => 'pending',
        ]);
    }

    /**
     * Override to send email verification via BrevoMailService.
     */
    public function sendEmailVerificationNotification(): void
    {
        $otp = $this->generateEmailOtp();
        app(BrevoMailService::class)->sendOtpEmail($this->email, $this->name, $otp);
    }

    /**
     * Send password reset notification via BrevoMailService instead of default SMTP.
     *
     * Bypasses Laravel's notification 'mail' channel entirely: that channel calls
     * toMail() and sends through the raw SMTP mailer, which does not use
     * BrevoMailService's HTTP-API-first (with SMTP fallback) delivery logic.
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        $html = view('emails.reset-password', [
            'name' => $this->name ?? 'User',
            'url'  => $url,
        ])->render();

        app(BrevoMailService::class)->send(
            $this->email,
            $this->name ?? 'User',
            'Reset Password - ' . config('app.name'),
            $html
        );
    }
}
