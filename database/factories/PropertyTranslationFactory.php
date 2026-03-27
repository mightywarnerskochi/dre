<?php

namespace Database\Factories;

use App\Models\CmsKit\PropertyTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyTranslation>
 */
class PropertyTranslationFactory extends Factory
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
            'language_code' => 'en',
            'title' => $this->faker->words(3, true),
            'description' => '<div>' . $this->faker->paragraphs(3, true) . '</div>',
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'community' => $this->faker->streetName(),
            'country' => 'United Arab Emirates',
            'postal_code' => $this->faker->postcode(),
            'full_address' => $this->faker->address(),
            'easy_to_access' => [
                ['icon' => 'fas fa-road', 'label' => 'Main Road Access'],
            ],
            'key_features' => [
                ['text' => $this->faker->sentence()],
            ],
            'amenities' => [
                ['icon' => 'fas fa-swimmer', 'name' => 'Swimming Pool'],
            ],
            'property_attributes' => [
                ['icon' => 'fas fa-parking', 'name' => 'Covered Parking'],
            ],
        ];
    }
}
