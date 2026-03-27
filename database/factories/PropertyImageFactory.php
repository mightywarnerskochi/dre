<?php

namespace Database\Factories;

use App\Models\CmsKit\PropertyImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyImage>
 */
class PropertyImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => \App\Models\CmsKit\Property::factory(),
            'image' => 'properties/images/' . $this->faker->uuid() . '.jpg',
            'alt_text' => $this->faker->sentence(),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'order' => $this->faker->numberBetween(1, 20),
        ];
    }
}
