<?php

namespace Database\Factories\CmsKit;

use App\Models\CmsKit\PropertyTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyTranslation>
 */
class PropertyTranslationFactory extends Factory
{
    protected function localizedContent(string $languageCode): array
    {
        if ($languageCode === 'ar') {
            return [
                'title' => 'شقة فاخرة بإطلالة مميزة',
                'description' => '<div><p>وحدة سكنية حديثة بتشطيبات راقية ومساحات واسعة.</p><p>تقع في موقع مميز بالقرب من الخدمات الأساسية ووسائل النقل.</p></div>',
                'address' => 'شارع الشيخ زايد',
                'city' => 'دبي',
                'community' => 'وسط المدينة',
                'country' => 'الإمارات العربية المتحدة',
                'full_address' => 'شارع الشيخ زايد، وسط المدينة، دبي، الإمارات العربية المتحدة',
                'easy_to_access' => [
                    ['icon' => 'fas fa-road', 'label' => 'وصول مباشر إلى الطريق الرئيسي'],
                ],
                'key_features' => [
                    ['text' => 'تشطيبات حديثة ومساحات معيشة مريحة'],
                ],
                'amenities' => [
                    ['icon' => 'fas fa-swimmer', 'name' => 'مسبح'],
                ],
                'property_attributes' => [
                    ['icon' => 'fas fa-parking', 'name' => 'مواقف مغطاة'],
                ],
            ];
        }

        return [
            'title' => $this->faker->words(3, true),
            'description' => '<div>'.$this->faker->paragraphs(3, true).'</div>',
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'community' => $this->faker->streetName(),
            'country' => 'United Arab Emirates',
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

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $languageCode = $this->faker->randomElement(['en', 'ar']);
        $content = $this->localizedContent($languageCode);

        return [
            'property_id' => null,
            'language_code' => $languageCode,
            'title' => $content['title'],
            'description' => $content['description'],
            'address' => $content['address'],
            'city' => $content['city'],
            'community' => $content['community'],
            'country' => $content['country'],
            'postal_code' => $this->faker->postcode(),
            'full_address' => $content['full_address'],
            'easy_to_access' => $content['easy_to_access'],
            'key_features' => $content['key_features'],
            'amenities' => $content['amenities'],
            'property_attributes' => $content['property_attributes'],
        ];
    }

    public function forProperty(int $propertyId): static
    {
        return $this->state(fn () => [
            'property_id' => $propertyId,
        ]);
    }

    public function english(): static
    {
        return $this->state(function () {
            $content = $this->localizedContent('en');

            return [
                'language_code' => 'en',
                'title' => $content['title'],
                'description' => $content['description'],
                'address' => $content['address'],
                'city' => $content['city'],
                'community' => $content['community'],
                'country' => $content['country'],
                'full_address' => $content['full_address'],
                'easy_to_access' => $content['easy_to_access'],
                'key_features' => $content['key_features'],
                'amenities' => $content['amenities'],
                'property_attributes' => $content['property_attributes'],
            ];
        });
    }

    public function arabic(): static
    {
        return $this->state(function () {
            $content = $this->localizedContent('ar');

            return [
                'language_code' => 'ar',
                'title' => $content['title'],
                'description' => $content['description'],
                'address' => $content['address'],
                'city' => $content['city'],
                'community' => $content['community'],
                'country' => $content['country'],
                'full_address' => $content['full_address'],
                'easy_to_access' => $content['easy_to_access'],
                'key_features' => $content['key_features'],
                'amenities' => $content['amenities'],
                'property_attributes' => $content['property_attributes'],
            ];
        });
    }
}
