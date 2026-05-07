<?php

namespace Database\Factories\CmsKit;

use App\Models\CmsKit\NearbyPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NearbyPlace>
 */
class NearbyPlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' '.$this->faker->randomElement(['School', 'Hospital', 'Park', 'Mall']),
            'type' => $this->faker->randomElement(['school', 'hospital', 'restaurant', 'attraction']),
            'latitude' => $this->faker->latitude(24.0, 26.0),
            'longitude' => $this->faker->longitude(54.0, 56.0),
            'address' => $this->faker->address(),
            'status' => true,
        ];
    }
}
