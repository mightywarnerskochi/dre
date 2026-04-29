<?php

use App\Support\MediaStorage;

if (! function_exists('media_url')) {
    /**
     * Public URL for a CMS media path (local public disk, S3, or CloudFront via AWS_URL).
     */
    function media_url(?string $path): ?string
    {
        return MediaStorage::url($path);
    }
}

if (! function_exists('dre_property_placeholder_image')) {
    /**
     * Fallback when a listing has no usable photos (sync with public/images/dre/property-placeholder.svg).
     */
    function dre_property_placeholder_image(): string
    {
        return asset('images/dre/property-placeholder.svg');
    }
}
