<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_picture',
        'status',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if admin is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Update last login information
     */
    public function updateLastLogin($ip = null, $userAgent = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
            'last_login_user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'username';
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    /**
     * Find admin by username or email for authentication
     */
    public function findForPassport($identifier)
    {
        return $this->where('username', $identifier)
                    ->orWhere('email', $identifier)
                    ->first();
    }
}
