<?php

namespace Database\Factories\CmsKit;

use App\Models\CmsKit\Agent;
use App\Models\CmsKit\NearbyPlace;
use App\Models\CmsKit\Property;
use App\Models\CmsKit\PropertyDetail;
use App\Models\CmsKit\PropertyTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (Property $property) {
            PropertyDetail::factory()
                ->forProperty($property->id)
                ->create();

            PropertyTranslation::factory()
                ->forProperty($property->id)
                ->english()
                ->create();

            PropertyTranslation::factory()
                ->forProperty($property->id)
                ->arabic()
                ->create();

            $places = NearbyPlace::factory()
                ->count($this->faker->numberBetween(2, 5))
                ->create();

            $syncData = [];

            foreach ($places as $index => $place) {
                $syncData[$place->id] = [
                    'distance' => $this->faker->randomFloat(2, 0.2, 15),
                    'order' => $index + 1,
                ];
            }

            $property->nearbyPlaces()->sync($syncData);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(3, true);
        $propId = 'PRP-'.$this->faker->unique()->numberBetween(1000, 9999);
        $imageCount = $this->faker->numberBetween(1, 5);
        $imageIndexes = range(1, $imageCount);

        return [
            'prop_id' => $propId,
            'title' => ucfirst($title),
            'slug' => Str::slug($title),
            'property_type' => $this->faker->randomElement(['apartment', 'villa', 'townhouse', 'penthouse', 'office']),
            'listing_type' => $this->faker->randomElement(['rent', 'sale']),
            'source_type' => $this->faker->randomElement(['manual', 'sync']),
            'price' => $this->faker->randomFloat(2, 50000, 5000000),
            'currency' => 'AED',
            'bedrooms' => $this->faker->numberBetween(1, 6),
            'bathrooms' => $this->faker->numberBetween(1, 4),
            'sqft' => $this->faker->numberBetween(400, 5000),
            'address' => $this->faker->streetAddress(),
            'full_address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'community' => $this->faker->streetName(),
            'country' => 'United Arab Emirates',
            'latitude' => $this->faker->latitude(24.0, 26.0),
            'longitude' => $this->faker->longitude(54.0, 56.0),
            'agent_id' => Agent::factory(),
            'status' => true,
            'order' => $this->faker->numberBetween(1, 100),
            'published_at' => now(),
            'images_directory' => 'properties/'.Str::slug($propId),
            'images' => implode(',', $imageIndexes),
        ];
    }
}
