<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sponsor_id',
        'level',
        'amount',
        'status',
        'package_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'level' => 'integer',
    ];

    /**
     * Get the user who earned the commission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sponsor who generated the commission
     */
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    /**
     * Get the package associated with the commission
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Scope for pending commissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved commissions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for specific level commissions
     */
    public function scopeOfLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Check if commission is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if commission is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
