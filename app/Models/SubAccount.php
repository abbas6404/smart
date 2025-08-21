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
        'generation_count',
        'total_balance',
        'withdrawal_limit',
        'total_withdrawal',
        'total_package_purchase',
        'total_deposit',
        'total_sponsor_commission',
        'total_generation_commission',
        'total_auto_income',
        'profile_picture',
        'status',
        'last_balance_update_at',
    ];

    protected $casts = [
        'package_purcased_at' => 'datetime',
        'last_balance_update_at' => 'datetime',
        'total_balance' => 'decimal:2',
        'withdrawal_limit' => 'decimal:2',
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
}
