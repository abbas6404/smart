<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoBoard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'total_collotion_amount',
        'total_contributors',
        'total_distributed',
        'today_collotion_amount',
        'today_contributors',
        'today_distributed',
        'today_per_account_distributed',
        'distribution_date',
        'distributed_date',
        'status',
        'distribution_log',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'distribution_date' => 'date',
        'distributed_date' => 'date',
        'total_collotion_amount' => 'decimal:2',
        'today_collotion_amount' => 'decimal:2',
        'today_per_account_distributed' => 'decimal:2',
        'distribution_log' => 'array',
    ];

    /**
     * Get the distributions for this auto board.
     */
    public function distributions()
    {
        return $this->hasMany(AutoBoardDistribution::class);
    }

    /**
     * Get the contributions for this auto board.
     */
    public function contributions()
    {
        return $this->hasMany(AutoBoardContribution::class);
    }

    /**
     * Check if the board is in collection status.
     */
    public function isCollecting(): bool
    {
        return $this->status === 'collotion';
    }

    /**
     * Check if the board is distributed.
     */
    public function isDistributed(): bool
    {
        return $this->status === 'distributed';
    }

    /**
     * Check if the board is ready for distribution.
     */
    public function isReadyForDistribution(): bool
    {
        return $this->isCollecting() && $this->today_collotion_amount > 0;
    }
}
