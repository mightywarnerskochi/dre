<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $fillable = [
        'provider',
        'access_token',
        'token_type',
        'expires_in',
        'issued_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->lte(now()->addMinute());
    }
}
