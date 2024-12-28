<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gender' => $gender = Gender::random(),
            'name' => "Tournament {$this->faker->unique()->name}",
        ];
    }

    public function gender(Gender $gender): static
    {
        return $this->state(fn(array $attributes) => [
            'gender' => $gender,
        ]);
    }

    public function withPlayers(Gender $gender, int $count): static
    {
        return $this->gender($gender)
            ->has(Player::factory($count)->gender($gender));
    }
}
