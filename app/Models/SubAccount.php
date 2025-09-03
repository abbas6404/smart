<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'account_number',
        'referral_code',
        'active_package_id',
        'active_package_purcased_at',
        'referral_by_id',
        'direct_referral_count',
        'purchase_referral_count',
        'generation_count',
        'total_balance',
        'remaining_withdrawal_limit',
        'total_withdrawal',
        'total_package_purchase',
        'total_deposit',
        'total_sponsor_commission',
        'total_generation_commission',
        'total_auto_income',
        'profile_picture',
        'status',
        'is_primary',
        'last_balance_update_at',
    ];

    protected $casts = [
        'package_purcased_at' => 'datetime',
        'last_balance_update_at' => 'datetime',
        'total_balance' => 'decimal:2',
        'remaining_withdrawal_limit' => 'decimal:2',    
        'total_withdrawal' => 'decimal:2',
        'total_package_purchase' => 'decimal:2',
        'total_deposit' => 'decimal:2',
        'total_sponsor_commission' => 'decimal:2',
        'total_generation_commission' => 'decimal:2',
        'total_auto_income' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'active_package_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(SubAccount::class, 'referral_by_id');
    }

    public function downline()
    {
        return $this->hasMany(SubAccount::class, 'referral_by_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function packagePurchases()
    {
        return $this->hasMany(PackagePurchase::class);
    }

    /**
     * Get package purchases made by direct referrals of this account
     */
    public function referralPackagePurchases()
    {
        // Get packages purchased by people who were referred by this account
        return PackagePurchase::whereHas('subAccount', function($query) {
            $query->where('referral_by_id', $this->id);
        });
    }

    /**
     * Get referral performance statistics
     */
    public function getReferralStats()
    {
        $referralPurchases = $this->referralPackagePurchases();
        
        return [
            'direct_referrals' => $this->direct_referral_count,
            'purchase_referrals' => $this->purchase_referral_count,
            'total_referral_value' => $referralPurchases->sum('amount'),
            'active_referral_packages' => $referralPurchases
                ->where('status', 'active')
                ->count(),
            'monthly_referral_income' => $this->calculateMonthlyReferralIncome(),
            'this_month_purchases' => $referralPurchases
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];
    }

    /**
     * Calculate monthly income from referrals
     */
    public function calculateMonthlyReferralIncome()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $monthlyPurchases = $this->referralPackagePurchases()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
            
        return $monthlyPurchases * 0.1; // Assuming 10% commission rate
    }

    /**
     * Increment purchase referral count when someone buys package through this referral
     */
    public function incrementPurchaseReferralCount()
    {
        $this->increment('purchase_referral_count');
    }

    /**
     * Get top performing referrals (by package purchases)
     */
    public function getTopReferrals($limit = 5)
    {
        return $this->downline()
            ->withCount('packagePurchases')
            ->orderBy('package_purchases_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent referral activities
     */
    public function getRecentReferralActivities($limit = 10)
    {
        return $this->referralPackagePurchases()
            ->with(['subAccount', 'package'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
