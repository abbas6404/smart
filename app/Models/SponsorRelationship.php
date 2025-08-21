<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsorRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sponsor_id',
        'level',
        'is_active',
        'joined_at',
        'notes',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    /**
     * Get the user in this relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sponsor in this relationship
     */
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    /**
     * Scope for active relationships
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific level relationships
     */
    public function scopeOfLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for relationships joined on specific date
     */
    public function scopeJoinedOn($query, $date)
    {
        return $query->whereDate('joined_at', $date);
    }

    /**
     * Check if relationship is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get the level of this relationship
     */
    public function getLevel(): int
    {
        return $this->level;
    }
}
