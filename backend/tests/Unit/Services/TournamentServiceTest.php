<?php

namespace Tests\Unit\Services;

use App\Enums\Gender;
use App\Exceptions\GenderNotSameException;
use App\Exceptions\TournamentException;
use App\Models\Game;
use App\Models\Player;
use App\Models\Tournament;
use App\Services\GameService;
use App\Services\PlayerService;
use App\Services\TournamentService;
use Database\Factories\TournamentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use InvalidArgumentException;
use Tests\TestCase;
use TypeError;

class TournamentServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_success()
    {
        $gender = Gender::random();
        $name = $this->faker->name($gender);
        $players = Player::factory()->gender($gender)->count(10)->make();

        $tournament = app(TournamentService::class)->create($name, $gender, $players->toArray());

        $this->assertNotNull($tournament);
        $this->assertTrue($tournament->exists);
        $this->assertTrue($tournament->players->count() === 10);
        $this->assertTrue($tournament->players->first()->tournament_id === $tournament->id);
        $this->assertFalse($tournament->players->first()->isDirty());
    }
}
