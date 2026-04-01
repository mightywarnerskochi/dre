<?php

namespace App\Models\CmsKit;

use Database\Factories\CmsKit\PropertyTranslationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected static function newFactory()
    {
        return PropertyTranslationFactory::new();
    }

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
        'postal_code',
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
