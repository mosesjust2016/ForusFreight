<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'phone_otp_expires_at' => 'datetime',
            'email_otp_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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

        if (app()->environment('local')) {
            $otp = '123456'; // dev bypass
        }

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

        if (app()->environment('local')) {
            $otp = '654321'; // dev bypass
        }

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

    /**
     * Override MustVerifyEmail notification to use our custom OTP mail.
     */
    public function sendEmailVerificationNotification(): void
    {
        $otp = $this->generateEmailOtp();

        // We'll send via a custom notification or direct mailable
        // For now, log it locally and let the controller handle sending
        Log::info("Email OTP for {$this->email}: {$otp}");
    }
}
