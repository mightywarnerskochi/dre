<?php

use App\Models\CmsKit\Blog;
use App\Models\CmsKit\Property;

return [

    /*
    |--------------------------------------------------------------------------
    | Observed Models
    |--------------------------------------------------------------------------
    |
    | List the models that should trigger an automated sitemap update when
    | they are created, updated, or deleted.
    |
    */

    'models' => [
        // Simple format (requires getSitemapUrl() or 'url' attribute on model)
        // \App\Models\CmsKit\Testimonial::class,

        // Dynamic format (specify URL prefix and optionally slug field)
        Blog::class => [
            'url_prefix' => '/blogs/',
            'slug_field' => 'slug', // optional, defaults to 'slug'
        ],
        Property::class => [
            'url_prefix' => '/properties/',
            'slug_field' => 'slug', // optional, defaults to 'slug'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Define the middleware that should be applied to the sitemap admin routes.
    | Default is ['web', 'cms.auth'].
    |
    */

    'middleware' => ['web', 'cms.auth'],

];
