<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'description',
        'withdrawal_limit',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'withdrawal_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function subAccounts()
    {
        return $this->hasMany(SubAccount::class);
    }

    public function packagePurchases()
    {
        return $this->hasMany(PackagePurchase::class);
    }
}
