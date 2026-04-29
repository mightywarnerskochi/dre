<?php

namespace App\Models\CmsKit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeBannerFilterDefinition extends Model
{
    protected $table = 'home_banner_filter_definitions';

    protected $fillable = [
        'key',
        'label',
        'translations',
        'ui_type',
        'source_table',
        'source_column',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'translations' => 'array',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(HomeBannerFilterValue::class, 'filter_definition_id');
    }
}

