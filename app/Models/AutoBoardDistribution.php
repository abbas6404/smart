<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoBoardDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'auto_board_id',
        'distribution_date',
        'total_amount',
        'distributed_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'distributed_amount' => 'decimal:2',
        'distribution_date' => 'date',
    ];

    /**
     * Get the auto board associated with the distribution
     */
    public function autoBoard()
    {
        return $this->belongsTo(AutoBoard::class);
    }

    /**
     * Get the contributions for this distribution
     */
    public function contributions()
    {
        return $this->hasMany(AutoBoardContribution::class);
    }

    /**
     * Scope for completed distributions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending distributions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for distributions on specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('distribution_date', $date);
    }

    /**
     * Check if distribution is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if distribution is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get remaining amount to distribute
     */
    public function getRemainingAmount(): float
    {
        return $this->total_amount - $this->distributed_amount;
    }
}
