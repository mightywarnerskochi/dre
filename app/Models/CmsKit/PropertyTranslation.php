<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'language_code',
        'title',
        'description',
        'address',
        'full_address',
        'city',
        'community',
        'country',
        'zip_code',
        'easy_to_access',
        'key_features',
        'amenities',
        'property_attributes',
    ];

    protected $casts = [
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
