<?php

namespace Database\Factories;

use App\Models\CmsKit\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(3, true);
        return [
            'prop_id' => 'PRP-' . $this->faker->unique()->numberBetween(1000, 9999),
            'title' => ucfirst($title),
            'slug' => \Illuminate\Support\Str::slug($title),
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
            'agent_id' => \App\Models\CmsKit\Agent::factory(),
            'status' => true,
            'order' => $this->faker->numberBetween(1, 100),
            'published_at' => now(),
        ];
    }
}
