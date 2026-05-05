<?php

namespace Database\Factories\CmsKit;

use App\Models\CmsKit\CareerDepartment;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CareerDepartment>
 */
class CareerDepartmentFactory extends Factory
{
    protected $model = CareerDepartment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->randomElement([
            'Design',
            'Sales',
            'Marketing',
            'Operations',
            'Human Resources',
            'Property Management',
            'Customer Service',
        ]);

        $arabicFaker = FakerFactory::create('ar_SA');

        return [
            'translations' => [
                'en' => [
                    'title' => $title,
                    'description' => $this->faker->sentence(12),
                ],
                'ar' => [
                    'title' => $arabicFaker->jobTitle(),
                    'description' => $arabicFaker->sentence(12),
                ],
            ],
            'stats' => [],
            'extra_fields' => [],
            'order_index' => $this->faker->numberBetween(1, 100),
            'status' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => false,
        ]);
    }
}
