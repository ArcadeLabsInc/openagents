<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agent>
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
            'balance' => 0,
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'instructions' => $this->faker->sentence,
            'welcome_message' => $this->faker->sentence,
            'user_id' => User::factory(),
        ];
    }

    public function published(): self
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => now(),
        ]);
    }

    public function unpublished(): self
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
