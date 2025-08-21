<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagePurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_account_id',
        'package_id',
        'transaction_id',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the sub account associated with the purchase
     */
    public function subAccount()
    {
        return $this->belongsTo(SubAccount::class);
    }

    /**
     * Get the user through sub account
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, SubAccount::class, 'id', 'id', 'sub_account_id', 'user_id');
    }

    /**
     * Get the package that was purchased
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the transaction associated with the purchase
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Scope for active packages
     */
    public function scopeActive($query)
    {
        return $query->whereHas('package', function($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Scope for expired packages
     */
    public function scopeExpired($query)
    {
        return $query->whereHas('package', function($q) {
            $q->where('expires_at', '<', now());
        });
    }

    /**
     * Scope for pending packages
     */
    public function scopePending($query)
    {
        return $query->whereHas('transaction', function($q) {
            $q->where('status', 'pending');
        });
    }

    /**
     * Check if the package is active
     */
    public function isActive(): bool
    {
        return $this->package && $this->package->status === 'active';
    }

    /**
     * Check if the package is expired
     */
    public function isExpired(): bool
    {
        return $this->package && $this->package->expires_at && $this->package->expires_at < now();
    }
}
