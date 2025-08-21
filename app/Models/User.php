<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Transaction;
use App\Models\PackagePurchase;

class User extends Authenticatable
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
        'phone',
        'email',
        'password',
        'address',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's sub account (MLM account)
     */
    public function subAccount(): HasOne
    {
        return $this->hasOne(SubAccount::class);
    }

    /**
     * Get the user's transactions
     */
    public function transactions()
    {
        if (!$this->subAccount) {
            // Return an empty query builder instead of collection
            return Transaction::whereRaw('1 = 0');
        }
        return $this->subAccount->transactions();
    }

    /**
     * Get the user's package purchases
     */
    public function packagePurchases()
    {
        if (!$this->subAccount) {
            // Return an empty query builder instead of collection
            return PackagePurchase::whereRaw('1 = 0');
        }
        return $this->subAccount->packagePurchases();
    }

    /**
     * Get the user's active package through sub account
     */
    public function activePackage()
    {
        return $this->hasOneThrough(Package::class, SubAccount::class, 'user_id', 'id', 'id', 'active_package_id');
    }

    /**
     * Get the user's referrals through sub account
     */
    public function referrals()
    {
        return $this->hasManyThrough(User::class, SubAccount::class, 'referral_by_id', 'id', 'id', 'user_id');
    }

    /**
     * Get the user's sponsor through sub account
     */
    public function sponsor()
    {
        return $this->hasOneThrough(User::class, SubAccount::class, 'id', 'id', 'id', 'referral_by_id');
    }

    /**
     * Get the user's commissions
     */
    public function commissions()
    {
        if (!$this->subAccount) {
            // Return an empty query builder instead of collection
            return Transaction::whereRaw('1 = 0');
        }
        return $this->subAccount->transactions()->whereIn('type', ['sponsor_commission', 'generation_commission']);
    }

    /**
     * Get the user's auto board income
     */
    public function autoBoardIncome()
    {
        if (!$this->subAccount) {
            // Return an empty query builder instead of collection
            return Transaction::whereRaw('1 = 0');
        }
        return $this->subAccount->transactions()->where('type', 'auto_income');
    }

    /**
     * Get the user's auto board status
     */
    public function autoBoardStatus()
    {
        $subAccount = $this->subAccount;
        if (!$subAccount) {
            return [
                'is_active' => false,
                'status' => 'inactive',
                'collection_amount' => 0,
                'contributors' => 0,
                'eligible_accounts' => 0,
                'ready_for_distribution' => false,
            ];
        }

        return [
            'is_active' => $subAccount->status === 'active',
            'status' => $subAccount->status === 'active' ? 'collection' : 'inactive',
            'has_package' => !is_null($subAccount->active_package_id),
            'referral_count' => $subAccount->direct_referral_count,
            'collection_amount' => $subAccount->total_balance ?? 0,
            'contributors' => $subAccount->direct_referral_count ?? 0,
            'eligible_accounts' => $subAccount->direct_referral_count ?? 0,
            'ready_for_distribution' => ($subAccount->total_balance ?? 0) > 0,
        ];
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
     * Get user's support tickets
     */
    public function supportTickets()
    {
        // This would return support tickets from a support_tickets table
        // For now, return an empty query builder
        return \App\Models\SupportTicket::whereRaw('1 = 0');
    }

    /**
     * Join auto board
     */
    public function joinAutoBoard()
    {
        // This would implement the logic to join auto board
        // For now, just update the sub account status
        if ($this->subAccount) {
            $this->subAccount->update(['status' => 'active']);
        }
    }

    /**
     * Leave auto board
     */
    public function leaveAutoBoard()
    {
        // This would implement the logic to leave auto board
        // For now, just update the sub account status
        if ($this->subAccount) {
            $this->subAccount->update(['status' => 'inactive']);
        }
    }

    /**
     * Get user's network (referrals and their referrals)
     */
    public function getNetwork()
    {
        // This would implement the logic to get the user's network
        // For now, return basic referral data
        return [
            'direct_referrals' => $this->referrals()->count(),
            'total_network' => 0, // Would calculate total network size
            'levels' => [] // Would calculate each level
        ];
    }

    /**
     * Get user's tree structure
     */
    public function getTree()
    {
        // This would implement the logic to get the user's tree structure
        // For now, return basic tree data
        return [
            'user' => $this,
            'referrals' => $this->referrals()->with('activePackage')->get(),
            'levels' => [] // Would calculate tree levels
        ];
    }

    /**
     * Get user's notifications
     */
    public function notifications()
    {
        // This would return notifications from a notifications table
        // For now, return an empty query builder
        return \Illuminate\Notifications\DatabaseNotification::whereRaw('1 = 0');
    }
}
