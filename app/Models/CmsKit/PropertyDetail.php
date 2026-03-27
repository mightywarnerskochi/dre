<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'property_id',
        'description',
        'year_built',
        'security_deposit',
        'direct_from_owner',
        'easy_to_access',
        'key_features',
        'amenities',
        'property_attributes',
    ];

    protected $casts = [
        'year_built' => 'integer',
        'security_deposit' => 'decimal:2',
        'easy_to_access' => 'array',
        'key_features' => 'array',
        'amenities' => 'array',
        'property_attributes' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
