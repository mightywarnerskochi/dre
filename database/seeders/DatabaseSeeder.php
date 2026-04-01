<?php

namespace Database\Seeders;

use CMS\SiteManager\Database\Seeders\CmsRolesPermissionsSeeder;
use CMS\SiteManager\Database\Seeders\MetadataSeeder;
use Database\Seeders\CmsAboutPermissionsSeeder;
use Database\Seeders\HomeBannerFiltersSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CmsRolesPermissionsSeeder::class,
            CmsAboutPermissionsSeeder::class,
            MetadataSeeder::class,
            HomeBannerFiltersSeeder::class,
        ]);
    }
}
