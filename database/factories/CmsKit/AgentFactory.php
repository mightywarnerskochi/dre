<?php

namespace Database\Factories\CmsKit;

use App\Models\CmsKit\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agent>
 */
class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'whatsapp_number' => $this->faker->phoneNumber(),
            'designation' => $this->faker->jobTitle(),
            'experience' => $this->faker->numberBetween(1, 20).' years',
            'languages' => 'English, Arabic',
            'description' => $this->faker->paragraph(),
            'status' => true,
        ];
    }
}
