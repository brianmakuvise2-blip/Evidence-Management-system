<?php

namespace App\Models;

// Import necessary classes
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    // These are traits that add functionality to our model
    use HasApiTokens, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * This means we can create/update these fields in bulk
     */
    protected $fillable = [
        'employee_id',
        'badge_number',
        'name',
        'email',
        'password',
        'institution_id',
        'department_id',
        'job_title',
        'phone_work',
        'phone_mobile',
        'signature_path',
        'profile_photo',
        'mfa_enabled',
        'mfa_secret',
        'mfa_recovery_codes',
        'last_login_at',
        'last_login_ip',
        'login_history',
        'account_status',
        'archived_at',
        'archived_by',
        'suspension_reason',
        'password_changed_at',
        'password_expires_at',
        'last_password_change_at',
        'password_history_count',
        'data_access_scope',
        'failed_login_attempts',
        'lockout_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * These won't be shown when converting user to JSON/array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
        'mfa_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     * This tells Laravel how to treat certain fields
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'archived_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'password_expires_at' => 'datetime',
        'last_password_change_at' => 'datetime',
        'mfa_enabled' => 'boolean',
        'login_history' => 'array',
        'mfa_recovery_codes' => 'array',
        'failed_login_attempts' => 'integer',
        'lockout_expires_at' => 'datetime',
    ];

    /**
     * RELATIONSHIPS
     * These define how User connects to other tables
     */
    
    // A user belongs to one institution
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    // A user belongs to one department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // A user can be archived by another user
    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    // A user has many activity logs
    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

    /**
     * HELPER METHODS
     * These are custom functions we can use throughout the app
     */
    
    // Log user activity manually
    public function logActivity($action, $status = 'success', $details = [])
    {
        // Create a new activity log record
        return UserActivityLog::create([
            'user_id' => $this->id,
            'action' => $action,
            'ip_address' => request()->ip(), // Get current IP
            'user_agent' => request()->userAgent(), // Get browser info
            'details' => $details,
            'status' => $status,
        ]);
    }

    // Record when user logs in
    public function recordLogin()
    {
        // Get existing login history or start new array
        $loginHistory = $this->login_history ?? [];
        
        // Add new login to beginning of array
        array_unshift($loginHistory, [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        // Keep only last 10 logins
        $loginHistory = array_slice($loginHistory, 0, 10);
        
        // Update user record
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'login_history' => $loginHistory,
        ]);
        
        // Log this activity
        $this->logActivity('login', 'success');
    }

    // Check if user needs MFA
    public function requiresMfa(): bool
    {
        // In production, you might want to force MFA for all
        // For now, just return whether they have it enabled
        return $this->mfa_enabled;
    }

    /**
     * Check if user's password has expired
     */
    public function isPasswordExpired(): bool
    {
        if (!$this->password_expires_at) {
            return false;
        }
        return now()->isAfter($this->password_expires_at);
    }

    /**
     * Check if password meets complexity requirements
     * Requirements: min 8 chars, 1 uppercase, 1 number, 1 special character
     */
    public static function validatePasswordComplexity($password): bool
    {
        $pattern = '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        return preg_match($pattern, $password) === 1;
    }

    /**
     * Get password complexity requirement message
     */
    public static function getPasswordRequirements(): string
    {
        return 'Password must be at least 8 characters with 1 uppercase letter, 1 number, and 1 special character (@$!%*?&)';
    }

    /**
     * Determine whether the current user account is temporarily locked.
     */
    public function isLockedOut(): bool
    {
        return $this->lockout_expires_at && now()->lessThan($this->lockout_expires_at);
    }

    /**
     * Reset failed login attempt counters after a successful login.
     */
    public function resetFailedLoginAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'lockout_expires_at' => null,
        ]);
    }

    /**
     * Unlock accounts automatically when lockout expires.
     */
    public function unlockIfExpired(): void
    {
        if ($this->lockout_expires_at && now()->greaterThanOrEqualTo($this->lockout_expires_at)) {
            $this->resetFailedLoginAttempts();
        }
    }

    /**
     * Record a failed login attempt and lock the account when the threshold is reached.
     */
    public function recordFailedLoginAttempt(int $maxAttempts = 5, int $lockoutMinutes = 15): void
    {
        $attempts = $this->failed_login_attempts + 1;
        $attributes = ['failed_login_attempts' => $attempts];

        if ($attempts >= $maxAttempts) {
            $attributes['lockout_expires_at'] = now()->addMinutes($lockoutMinutes);
        }

        $this->update($attributes);

        $lockedUntil = $attributes['lockout_expires_at'] ?? null;

        $this->logActivity('login_failure', 'failure', [
            'attempts' => $attempts,
            'locked_until' => $lockedUntil ? $lockedUntil->toDateTimeString() : null,
        ]);

        if ($lockedUntil) {
            $this->logActivity('account_lockout', 'warning', [
                'locked_until' => $lockedUntil->toDateTimeString(),
            ]);

            // Notify all administrators about the account lockout
            $administrators = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['administrator', 'system-administrator']);
            })->get();

            foreach ($administrators as $admin) {
                $admin->notify(new \App\Notifications\AccountLockoutNotification($this, $lockedUntil));
            }
        }
    }

    /**
     * Update password with expiry date (90 days)
     */
    public function updatePassword($newPassword, $expiryDays = 90): bool
    {
        if (!self::validatePasswordComplexity($newPassword)) {
            throw new \Exception(self::getPasswordRequirements());
        }

        $this->update([
            'password' => bcrypt($newPassword),
            'last_password_change_at' => now(),
            'password_expires_at' => now()->addDays($expiryDays),
            'password_history_count' => $this->password_history_count + 1,
        ]);

        return true;
    }

    /**
     * Verify MFA TOTP code
     */
    public function verifyMfaCode($code): bool
    {
        if (!$this->mfa_enabled || !$this->mfa_secret) {
            return false;
        }

        return \App\Services\MfaService::verifyCode($this->mfa_secret, $code);
    }

    /**
     * Generate MFA recovery codes
     */
    public function generateMfaRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = bin2hex(random_bytes(4));
        }
        
        $this->update([
            'mfa_recovery_codes' => $codes,
        ]);

        return $codes;
    }

    /**
     * Use MFA recovery code
     */
    public function useMfaRecoveryCode($code): bool
    {
        $codes = $this->mfa_recovery_codes ?? [];
        
        if (in_array($code, $codes)) {
            // Remove used code
            $codes = array_diff($codes, [$code]);
            $this->update(['mfa_recovery_codes' => $codes]);
            return true;
        }

        return false;
    }
}