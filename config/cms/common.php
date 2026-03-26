<?php

return [
    'name' => 'Distinguished Real Estate',
    'theme' => [
        'primary_color' => '#0861c5',
        'secondary_color' => '#212529',
        'background_color' => '#f8f9fa',
        'sidebar_color' => '#343a40',
        'text_color' => '#212529',
    ],

    'auth' => [
        'admin_name' => env('CMS_ADMIN_NAME', 'Admin User'),
        'admin_email' => env('CMS_ADMIN_EMAIL', 'admin@example.com'),
        'prefix' => 'admin',
        'middleware' => ['web'],
    ],

    'modules' => [
        'about' => true,
        'mission-vision' => true,
        'why-choose-us' => true,
        'successful-journeys' => true,
        'testimonials' => false,
        'languages' => true,
        'metadata' => true,
        'site-information' => true,
        'sitemap' => true,
        'banners' => true,
        'faqs' => false,
        'enquiries' => true,
        'locations' => true,
        'brands' => false,
        'newsletter-signups' => false,
        'blogs' => true,
        'careers' => true,
        'properties' => true,
        'agents' => true,
        'nearby-places' => true,
    ],

    'careers' => [
        'common_section' => true,
        'vacancies' => true,
        'departments' => true,
        'candidates' => true,
    ],

    'tinymce' => [
        'selector' => '.tinymce-editor',
        'plugins' => 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
        'toolbar' => 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    ],
];
