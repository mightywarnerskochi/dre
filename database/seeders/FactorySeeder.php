<?php

namespace Database\Seeders;

use App\Models\CmsKit\Blog;
use App\Models\CmsKit\Career;
use App\Models\CmsKit\CareerDepartment;
use App\Models\CmsKit\Agent;
use App\Models\CmsKit\NearbyPlace;
use App\Models\CmsKit\Property;
use App\Models\CmsKit\PropertyDetail;
use App\Models\CmsKit\PropertyTranslation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class FactorySeeder extends Seeder
{
    /**
     * Run sample data factories.
     */
    public function run(): void
    {
        User::factory()->count(10)->create();
        Agent::factory()->count(25)->create();
        CareerDepartment::factory()->count(8)->create();
        Career::factory()->count(10)->create();
        Blog::factory()->count(20)->create();
        NearbyPlace::factory()->count(25)->create();

        $this->resetPropertyTables();

        $properties = Property::factory()->count(100)->create();

        $this->seedPropertyRelations($properties);
    }

    protected function resetPropertyTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('property_nearby_places')->truncate();
        DB::table('property_translations')->truncate();
        DB::table('property_details')->truncate();
        DB::table('properties')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @param Collection<int, Property> $properties
     */
    protected function seedPropertyRelations(Collection $properties): void
    {
        foreach ($properties as $property) {
            if (! $property->details()->exists()) {
                PropertyDetail::factory()
                    ->forProperty($property->id)
                    ->create();
            }

            foreach (['en', 'ar'] as $languageCode) {
                if ($property->translations()->where('language_code', $languageCode)->exists()) {
                    continue;
                }

                PropertyTranslation::factory()
                    ->forProperty($property->id)
                    ->{$languageCode === 'en' ? 'english' : 'arabic'}()
                    ->create();
            }

            $places = NearbyPlace::factory()->count(3)->create();
            $syncData = [];

            foreach ($places as $index => $place) {
                $syncData[$place->id] = [
                    'distance' => fake()->randomFloat(2, 0.2, 15),
                    'order' => $index + 1,
                ];
            }

            $property->nearbyPlaces()->syncWithoutDetaching($syncData);
        }
    }
}
