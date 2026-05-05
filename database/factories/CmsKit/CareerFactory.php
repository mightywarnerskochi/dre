<?php

namespace Database\Factories\CmsKit;

use App\Models\CmsKit\Career;
use App\Models\CmsKit\CareerDepartment;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Career>
 */
class CareerFactory extends Factory
{
    protected $model = Career::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->jobTitle();
        $location = $this->faker->randomElement(['Dubai, UAE', 'Sharjah, UAE', 'Abu Dhabi, UAE']);
        $department = CareerDepartment::query()
            ->where('status', true)
            ->inRandomOrder()
            ->first() ?? CareerDepartment::factory()->create();
        $departmentTitle = trim((string) $department->getTranslation('title', 'en'));
        $arabicFaker = FakerFactory::create('ar_SA');

        return [
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1000, 9999),
            'job_type' => $this->faker->randomElement(array_keys(config('cms-kit.database.careers.items.job_type_options', [
                'full_time' => [],
                'part_time' => [],
                'remote' => [],
            ]))),
            'department' => $departmentTitle !== '' ? $departmentTitle : 'Design',
            'location' => $location,
            'country' => '',
            'flag_image' => null,
            'flag_alt' => 'Location flag',
            'base' => $this->faker->randomElement(array_keys(config('cms-kit.database.careers.items.base_options', [
                'temporary' => [],
                'permanent' => [],
                'contract' => [],
            ]))),
            'published_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'order_index' => $this->faker->numberBetween(1, 100),
            'status' => true,
            'translations' => [
                'en' => $this->localizedContent('en', $title, $location),
                'ar' => $this->localizedContent('ar', $arabicFaker->jobTitle(), $arabicFaker->city()),
            ],
            'metadata' => [
                'meta_title' => $title,
                'meta_description' => $this->faker->sentence(12),
                'meta_keywords' => implode(', ', $this->faker->words(5)),
                'og_title' => $title,
                'og_description' => $this->faker->sentence(12),
                'og_image' => null,
            ],
            'extra_fields' => [],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function localizedContent(string $languageCode, string $title, string $location): array
    {
        $faker = $languageCode === 'ar' ? FakerFactory::create('ar_SA') : $this->faker;

        return [
            'title' => $title,
            'short_description' => $faker->sentence(12),
            'location' => $location,
            'about' => '<p>'.$faker->paragraph(4).'</p>',
            'responsibilities' => '<ul><li>'.implode('</li><li>', $faker->sentences(4)).'</li></ul>',
            'requirements' => '<ul><li>'.implode('</li><li>', $faker->sentences(4)).'</li></ul>',
            'join_the_team' => '<p>'.$faker->paragraph(3).'</p>',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => false,
        ]);
    }

    public function withDepartment(CareerDepartment|int|string $department): static
    {
        if ($department instanceof CareerDepartment) {
            $department = $department->getTranslation('title', 'en');
        } elseif (is_int($department)) {
            $department = CareerDepartment::find($department)?->getTranslation('title', 'en') ?? (string) $department;
        }

        return $this->state(fn () => [
            'department' => $department,
        ]);
    }
}
