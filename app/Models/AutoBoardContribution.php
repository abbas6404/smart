<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoBoardContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sub_account_id',
        'auto_board_id',
        'amount',
        'contribution_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'contribution_date' => 'date',
    ];

    /**
     * Get the user who made the contribution
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sub account associated with the contribution
     */
    public function subAccount()
    {
        return $this->belongsTo(SubAccount::class);
    }

    /**
     * Get the auto board associated with the contribution
     */
    public function autoBoard()
    {
        return $this->belongsTo(AutoBoard::class);
    }

    /**
     * Scope for active contributions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for pending contributions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for contributions on specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('contribution_date', $date);
    }

    /**
     * Check if contribution is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if contribution is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
