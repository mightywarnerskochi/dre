<?php

namespace Database\Factories\CmsKit;

use App\Models\CmsKit\PropertyDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyDetail>
 */
class PropertyDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => null,
            'description' => '<div>'.$this->faker->paragraphs(3, true).'</div>',
            'year_built' => $this->faker->numberBetween(2000, 2025),
            'security_deposit' => $this->faker->randomFloat(2, 1000, 50000),
            'virtual_tour_url' => $this->faker->optional()->url(),
            'direct_from_owner' => $this->faker->randomElement(['Yes, Owner Listed', 'No', 'Exclusive Owner Unit']),
            'easy_to_access' => [
                ['icon' => 'fas fa-road', 'label' => 'Main Road Access'],
                ['icon' => 'fas fa-train', 'label' => 'Metro Station Nearby'],
            ],
            'key_features' => [
                ['text' => $this->faker->sentence()],
                ['text' => $this->faker->sentence()],
            ],
            'amenities' => [
                ['icon' => 'fas fa-swimmer', 'name' => 'Swimming Pool'],
                ['icon' => 'fas fa-dumbbell', 'name' => 'Gymnasium'],
            ],
            'property_attributes' => [
                ['icon' => 'fas fa-wind', 'name' => 'Central Cooling'],
                ['icon' => 'fas fa-parking', 'name' => 'Covered Parking'],
            ],
        ];
    }

    public function forProperty(int $propertyId): static
    {
        return $this->state(fn () => [
            'property_id' => $propertyId,
        ]);
    }
}
