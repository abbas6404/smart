<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class KYCVerification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nid_number',
        'nid_type',
        'full_name_bangla',
        'full_name_english',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'address',
        'postal_code',
        'nid_front_image',
        'nid_back_image',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reviewed_at' => 'datetime',
    ];

    // NID Types
    const NID_TYPES = [
        'smart_nid' => 'Smart NID',
        'old_nid' => 'Old NID',
        'birth_certificate' => 'Birth Certificate'
    ];

    // Verification Statuses
    const STATUSES = [
        'kyc_pending' => 'KYC Pending',
        'kyc_verified' => 'KYC Verified',
        'kyc_failed' => 'KYC Failed'
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who reviewed this verification
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    /**
     * Check if the verification is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'kyc_verified';
    }

    /**
     * Check if the verification is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'kyc_pending';
    }

    /**
     * Check if the verification is verified
     */
    public function isVerified(): bool
    {
        return $this->status === 'kyc_verified';
    }

    /**
     * Check if the verification failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'kyc_failed';
    }

    /**
     * Get the age of the person
     */
    public function getAge(): int
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : 0;
    }

    /**
     * Check if the person is a minor (under 18)
     */
    public function isMinor(): bool
    {
        return $this->getAge() < 18;
    }

    /**
     * Check if the person is a senior citizen (65+)
     */
    public function isSeniorCitizen(): bool
    {
        return $this->getAge() >= 65;
    }

    /**
     * Get the full address
     */
    public function getFullAddress(): string
    {
        return $this->address ?? '';
    }

    /**
     * Get the full name in English
     */
    public function getFullNameEnglish(): string
    {
        return trim($this->full_name_english);
    }

    /**
     * Get the full name in Bengali
     */
    public function getFullNameBangla(): string
    {
        return trim($this->full_name_bangla ?? '');
    }

    /**
     * Scope for pending verifications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'kyc_pending');
    }

    /**
     * Scope for verified verifications
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'kyc_verified');
    }

    /**
     * Scope for failed verifications
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'kyc_failed');
    }

    /**
     * Get verification statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'pending' => self::where('status', 'kyc_pending')->count(),
            'verified' => self::where('status', 'kyc_verified')->count(),
            'failed' => self::where('status', 'kyc_failed')->count(),
        ];
    }
}
