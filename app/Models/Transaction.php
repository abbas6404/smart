<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_account_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'checked_by',
        'checked_at',
        'checked_notes',
        'description',
        'metadata',
        'status',
        'system_ip',
        'system_user_agent',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'checked_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the sub account associated with the transaction
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
     * Get the admin who checked the transaction
     */
    public function checkedBy()
    {
        return $this->belongsTo(Admin::class, 'checked_by');
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved transactions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected transactions
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for specific transaction types
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for commission transactions
     */
    public function scopeCommissions($query)
    {
        return $query->whereIn('type', ['sponsor_commission', 'generation_commission']);
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if transaction is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if transaction is a commission
     */
    public function isCommission(): bool
    {
        return in_array($this->type, ['sponsor_commission', 'generation_commission']);
    }
}
