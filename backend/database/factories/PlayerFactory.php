<?php

namespace Database\Factories;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $config = config('tournament.player_units');
        return [
            'dni' => $this->faker->unique()->randomNumber,
            'age' => $this->faker->numberBetween(18, 60),
            'last_name' => $this->faker->lastName,
        ];
    }

    public function gender(Gender $gender): static
    {
        return $gender == Gender::Male ? $this->male() : $this->female();
    }

    public function anyGender(): static
    {
        return $this->gender(Gender::random());
    }

    public function female(): static
    {
        $config = config('tournament.player_units');

        return $this->state(fn(array $attributes) => [
            'first_name' => $this->faker->firstName(Gender::Female),
            'gender' => Gender::Female,
            'skill' => $this->faker->randomFloat(
                2,
                $config['skill']['min'],
                $config['skill']['max']
            ),
            'latency' => $this->faker->randomFloat(
                2,
                $config['latency']['min'],
                $config['latency']['max']
            ),
        ]);
    }

    public function male(): static
    {
        $config = config('tournament.player_units');

        return $this->state(fn(array $attributes) => [
            'first_name' => $this->faker->firstName(Gender::Male),
            'gender' => Gender::Male,
            'skill' => $this->faker->randomFloat(
                2,
                $config['skill']['min'],
                $config['skill']['max']
            ),
            'strength' => $this->faker->randomFloat(
                2,
                $config['strength']['min'],
                $config['strength']['max']
            ),
            'speed' => $this->faker->randomFloat(
                2,
                $config['speed']['min'],
                $config['speed']['max']
            ),
        ]);
    }
}
