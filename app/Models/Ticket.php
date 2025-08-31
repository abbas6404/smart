<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'message',
        'attachments',
        'reply',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'attachments' => 'array', // Store file paths as JSON array
    ];

    // Ticket Statuses
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    const STATUSES = [
        self::STATUS_OPEN => 'Open',
        self::STATUS_CLOSED => 'Closed',
    ];

    /**
     * Get the user who created this ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * Get the admin who reviewed this ticket
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    /**
     * Check if the ticket is open
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if the ticket is closed
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Check if the ticket has been replied to
     */
    public function hasReply(): bool
    {
        return !empty($this->reply);
    }

    /**
     * Check if the ticket has attachments
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments) && is_array($this->attachments);
    }

    /**
     * Get attachment count
     */
    public function getAttachmentCount(): int
    {
        return $this->hasAttachments() ? count($this->attachments) : 0;
    }

    /**
     * Get the time since ticket was created
     */
    public function getTimeSinceCreated(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    /**
     * Get the time since ticket was reviewed
     */
    public function getTimeSinceReviewed(): string
    {
        return $this->reviewed_at ? $this->reviewed_at->diffForHumans() : '';
    }

    /**
     * Check if ticket is overdue (open for more than 24 hours)
     */
    public function isOverdue(): bool
    {
        if (!$this->isOpen()) {
            return false;
        }
        
        return $this->created_at && $this->created_at->diffInHours(now()) > 24;
    }

    /**
     * Get ticket priority based on age
     */
    public function getPriority(): string
    {
        if (!$this->isOpen()) {
            return 'closed';
        }

        $hours = $this->created_at ? $this->created_at->diffInHours(now()) : 0;

        if ($hours > 48) {
            return 'high';
        } elseif ($hours > 24) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope for closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    /**
     * Scope for overdue tickets
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OPEN)
                    ->where('created_at', '<', now()->subHours(24));
    }

    /**
     * Scope for tickets by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for tickets by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for tickets reviewed by admin
     */
    public function scopeReviewedBy($query, $adminId)
    {
        return $query->where('reviewed_by', $adminId);
    }

    /**
     * Scope for tickets without reply
     */
    public function scopeWithoutReply($query)
    {
        return $query->whereNull('reply');
    }

    /**
     * Scope for tickets with reply
     */
    public function scopeWithReply($query)
    {
        return $query->whereNotNull('reply');
    }

    /**
     * Get ticket statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'open' => self::where('status', self::STATUS_OPEN)->count(),
            'closed' => self::where('status', self::STATUS_CLOSED)->count(),
            'overdue' => self::overdue()->count(),
            'without_reply' => self::withoutReply()->count(),
            'with_reply' => self::withReply()->count(),
        ];
    }

    /**
     * Get category-wise statistics
     */
    public static function getCategoryStatistics(): array
    {
        return self::selectRaw('category_id, COUNT(*) as total, status')
                   ->with('category')
                   ->groupBy('category_id', 'status')
                   ->get()
                   ->groupBy('category_id');
    }
}

