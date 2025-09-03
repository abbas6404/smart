<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'address',
        'profile_picture',
        'agent_account_number',
        'total_balance',
        'total_debit',
        'total_credit',
        'safety_balance',
        'status',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string, string>
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'total_balance' => 'decimal:2',
            'total_debit' => 'decimal:2',
            'total_credit' => 'decimal:2',
            'safety_balance' => 'decimal:2',
        ];
    }

    /**
     * Check if agent is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if agent is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if agent is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Update last login information
     */
    public function updateLastLogin($ip = null, $userAgent = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
            'last_login_user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'agent_account_number';
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Find agent by phone or email for authentication
     */
    public function findForPassport($identifier)
    {
        return $this->where('phone', $identifier)
                    ->orWhere('email', $identifier)
                    ->first();
    }

    /**
     * Get available balance (total_balance - safety_balance)
     */
    public function getAvailableBalance(): float
    {
        return max(0, $this->total_balance - $this->safety_balance);
    }

    /**
     * Check if agent has sufficient balance
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->getAvailableBalance() >= $amount;
    }

    /**
     * Add credit to agent balance
     */
    public function addCredit(float $amount, string $description = null)
    {
        $this->increment('total_balance', $amount);
        $this->increment('total_credit', $amount);
        
        // Log transaction if needed
        // Transaction::create([...]);
    }

    /**
     * Add debit to agent balance
     */
    public function addDebit(float $amount, string $description = null)
    {
        $this->decrement('total_balance', $amount);
        $this->increment('total_debit', $amount);
        
        // Log transaction if needed
        // Transaction::create([...]);
    }

    /**
     * Scope for active agents
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive agents
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for suspended agents
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Get agent statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'active' => self::where('status', 'active')->count(),
            'inactive' => self::where('status', 'inactive')->count(),
            'suspended' => self::where('status', 'suspended')->count(),
            'total_balance' => self::sum('total_balance'),
            'total_credit' => self::sum('total_credit'),
            'total_debit' => self::sum('total_debit'),
        ];
    }
}
