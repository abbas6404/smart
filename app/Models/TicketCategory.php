<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all tickets in this category
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    /**
     * Get the count of open tickets in this category
     */
    public function getOpenTicketsCount(): int
    {
        return $this->tickets()->where('status', 'open')->count();
    }

    /**
     * Get the count of closed tickets in this category
     */
    public function getClosedTicketsCount(): int
    {
        return $this->tickets()->where('status', 'closed')->count();
    }

    /**
     * Get total tickets count in this category
     */
    public function getTotalTicketsCount(): int
    {
        return $this->tickets()->count();
    }

    /**
     * Scope for categories with open tickets
     */
    public function scopeWithOpenTickets($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', 'open');
        });
    }

    /**
     * Scope for categories with closed tickets
     */
    public function scopeWithClosedTickets($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', 'closed');
        });
    }

    /**
     * Get category statistics
     */
    public static function getStatistics(): array
    {
        $categories = self::withCount(['tickets as total_tickets'])
            ->withCount(['tickets as open_tickets' => function ($query) {
                $query->where('status', 'open');
            }])
            ->withCount(['tickets as closed_tickets' => function ($query) {
                $query->where('status', 'closed');
            }])
            ->get();

        return [
            'total_categories' => $categories->count(),
            'categories' => $categories,
            'total_tickets' => $categories->sum('total_tickets'),
            'total_open_tickets' => $categories->sum('open_tickets'),
            'total_closed_tickets' => $categories->sum('closed_tickets'),
        ];
    }
}

