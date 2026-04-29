<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeBannerFilterValue extends Model
{
    protected $table = 'home_banner_filter_values';

    protected $fillable = [
        'filter_definition_id',
        'value',
        'label',
        'translations',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'translations' => 'array',
    ];

    public function definition(): BelongsTo
    {
        return $this->belongsTo(HomeBannerFilterDefinition::class, 'filter_definition_id');
    }
}

