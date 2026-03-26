<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CmsAboutPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'about.view',
            'about.create',
            'about.edit',
            'about.delete',
            'mission-vision.view',
            'mission-vision.create',
            'mission-vision.edit',
            'mission-vision.delete',
            'why-choose-us.view',
            'why-choose-us.create',
            'why-choose-us.edit',
            'why-choose-us.delete',
            'successful-journeys.view',
            'successful-journeys.create',
            'successful-journeys.edit',
            'successful-journeys.delete',
            'property.view',
            'property.create',
            'property.edit',
            'property.delete',
            'property.status',
            'property.order',
            'agents.view',
            'agents.create',
            'agents.edit',
            'agents.delete',
            'nearby-places.view',
            'nearby-places.create',
            'nearby-places.edit',
            'nearby-places.delete',
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
