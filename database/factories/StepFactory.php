<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inputTypes = ['llm', 'vector_query'];
        $inputType = $this->faker->randomElement($inputTypes);

        $input = [
            'type' => $inputType,
            'model' => $inputType == 'llm' ? 'gpt-4' : null,
            'instruction' => $this->faker->sentence,
        ];

        $output = [
            'response' => implode("\n", $this->faker->sentences(3)),
            'tokens_used' => $this->faker->numberBetween(1000, 2000),
        ];

        return [
            'agent_id' => Agent::factory(),
            'task_id' => Task::factory(),
            'description' => $this->faker->sentence(),
            'input' => json_encode($input),
            'output' => json_encode($output),
        ];
    }
}
