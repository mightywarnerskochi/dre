<?php

use App\Models\CmsKit\Blog;
use App\Models\CmsKit\Career;
use App\Models\CmsKit\Property;

return [

    /*
    |--------------------------------------------------------------------------
    | Static Paths
    |--------------------------------------------------------------------------
    |
    | Public SPA routes that should always be included in the generated
    | sitemap. Dynamic model URLs are appended from the model config below.
    |
    */

    'static_paths' => [
        '/',
        '/about',
        '/our-property',
        '/insights',
        '/career',
        '/contact',
        '/map',
        '/book-a-viewing',
        '/terms-conditions',
        '/privacy-policy',
        '/disclaimer',
        '/cookie-policy',
    ],

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
            'url_prefix' => '/insights-details/',
            'slug_field' => 'slug', // optional, defaults to 'slug'
        ],
        Property::class => [
            'url_prefix' => '/property-details/',
            'slug_field' => 'slug', // optional, defaults to 'slug'
        ],
        Career::class => [
            'url_prefix' => '/career-details/',
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
