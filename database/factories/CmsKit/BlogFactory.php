<?php

namespace Database\Factories\CmsKit;

use App\Models\CmsKit\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(5);
        $slug = Str::slug($title);

        return [
            'slug' => $slug,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'feature_image' => "blogs/{$slug}/feature.jpg",
            'feature_image_alt' => $title,
            'detail_image' => "blogs/{$slug}/detail.jpg",
            'detail_image_alt' => $title,
            'banner_image' => "blogs/{$slug}/banner.jpg",
            'banner_alt' => $title,
            'image_3' => "blogs/{$slug}/image-3.jpg",
            'image_3_alt' => $title,
            'image_4' => "blogs/{$slug}/image-4.jpg",
            'image_4_alt' => $title,
            'order_index' => $this->faker->numberBetween(1, 100),
            'status' => true,
            'translations' => [
                'en' => $this->localizedContent('en', $title),
                'ar' => $this->localizedContent('ar', $title),
            ],
            'extra_fields' => [],
            'metadata' => [
                'meta_title' => $title,
                'meta_description' => $this->faker->sentence(12),
                'meta_keywords' => implode(', ', $this->faker->words(5)),
                'og_title' => $title,
                'og_description' => $this->faker->sentence(12),
                'og_image' => "blogs/{$slug}/og.jpg",
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function localizedContent(string $languageCode, string $title): array
    {
        if ($languageCode === 'ar') {
            return [
                'title' => 'دليل الاستثمار العقاري في دبي',
                'content' => '<div><p>تعرف على أهم النصائح لاختيار العقار المناسب وتحقيق أفضل عائد من الاستثمار.</p></div>',
                'extra_fields' => [
                    'published_by' => 'فريق Dist3',
                    'type' => 'عقارات',
                ],
            ];
        }

        return [
            'title' => $title,
            'content' => '<div><p>'.$this->faker->paragraphs(4, true).'</p></div>',
            'extra_fields' => [
                'published_by' => 'Dist3Admin',
                'type' => $this->faker->randomElement(['Buying Properties', 'Real Estate', 'Investment']),
            ],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => false,
        ]);
    }
}
