<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Roles and Permissions
    |--------------------------------------------------------------------------
    |
    | This configuration defines the default roles and their module-level
    | permissions. The seeder uses this to populate the database.
    |
    */

    'roles' => [
        'superadmin' => [
            'name' => 'Super Admin',
            'permissions' => '*', // Special flag for all permissions
        ],
        'client' => [
            'name' => 'Client',
            'permissions' => [
            'about.create',
            'about.delete',
            'about.edit',
            'about.view',
            'agents.create',
            'agents.delete',
            'agents.edit',
            'agents.view',
            'banners.create',
            'banners.delete',
            'banners.edit',
            'banners.view',
            'blogs.create',
            'blogs.delete',
            'blogs.edit',
            'blogs.view',
            'brands.create',
            'brands.delete',
            'brands.edit',
            'brands.view',
            'careers.create',
            'careers.delete',
            'careers.edit',
            'careers.export',
            'careers.show',
            'careers.view',
            'enquiries.create',
            'enquiries.delete',
            'enquiries.edit',
            'enquiries.export',
            'enquiries.show',
            'enquiries.view',
            'faqs.create',
            'faqs.delete',
            'faqs.edit',
            'faqs.view',
            'locations.create',
            'locations.delete',
            'locations.edit',
            'locations.view',
            'metadata.create',
            'metadata.delete',
            'metadata.edit',
            'metadata.view',
            'mission-vision.create',
            'mission-vision.delete',
            'mission-vision.edit',
            'mission-vision.view',
            'nearby-places.create',
            'nearby-places.delete',
            'nearby-places.edit',
            'nearby-places.view',
            'neighborhoods.create',
            'neighborhoods.delete',
            'neighborhoods.edit',
            'neighborhoods.view',
            'newsletter.delete',
            'newsletter.view',
            'properties.create',
            'properties.delete',
            'properties.edit',
            'properties.view',
            'property.create',
            'property.delete',
            'property.edit',
            'property.order',
            'property.status',
            'property.view',
            'site-information.create',
            'site-information.delete',
            'site-information.edit',
            'site-information.view',
            'sitemap.create',
            'sitemap.delete',
            'sitemap.edit',
            'sitemap.view',
            'successful-journeys.create',
            'successful-journeys.delete',
            'successful-journeys.edit',
            'successful-journeys.view',
            'why-choose-us.create',
            'why-choose-us.delete',
            'why-choose-us.edit',
            'why-choose-us.view',
            ],
        ],
    ],

    'enquiries' => [
        'columns' => [
            'name' => true,
            'email' => true,
            'phone' => true,
            'company' => true,
            'country' => true,
            'page_source' => true,
            'page_url' => true,
            'message' => true,
            'subject' => true,
            'created_at' => true,
        ],
        'extra_fields' => [
            'subject' => ['label' => 'Subject', 'type' => 'text'],
            'interested_in' => ['label' => 'Interested In', 'type' => 'text'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Users
    |--------------------------------------------------------------------------
    |
    | Seed these users automatically.
    |
    */
    'users' => [
        [
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'superadmin',
        ],
        [
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => 'password',
            'role' => 'client',
        ],
    ],

    'defaults' => [
        'view',
        'edit',
        'create',
        'delete',
    ],
];
