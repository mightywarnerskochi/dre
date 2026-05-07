<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CmsAboutPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
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
            'home-banner-filters.create',
            'home-banner-filters.delete',
            'home-banner-filters.edit',
            'home-banner-filters.view',
            'languages.create',
            'languages.delete',
            'languages.edit',
            'languages.view',
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
            'permissions.create',
            'permissions.delete',
            'permissions.edit',
            'permissions.view',
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
            'roles.create',
            'roles.delete',
            'roles.edit',
            'roles.view',
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
            'users.create',
            'users.delete',
            'users.edit',
            'users.view',
            'why-choose-us.create',
            'why-choose-us.delete',
            'why-choose-us.edit',
            'why-choose-us.view',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission,
                'guard_name' => 'cms',
            ]);
        }

        $superAdmin = Role::where('name', 'superadmin')->where('guard_name', 'cms')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        $client = Role::where('name', 'client')->where('guard_name', 'cms')->first();
        if ($client) {
            $clientPermissions = config('cms-kit.permissions.roles.client.permissions', []);
            $assignable = Permission::where('guard_name', 'cms')
                ->whereIn('name', $clientPermissions)
                ->get();

            if ($assignable->isNotEmpty()) {
                $client->givePermissionTo($assignable);
            }
        }
    }
}
