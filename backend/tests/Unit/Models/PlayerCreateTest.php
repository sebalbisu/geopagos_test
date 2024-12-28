<?php

namespace Tests\Unit\Models;

use App\Enums\Gender;
use App\Exceptions\GenderNotSameException;
use App\Models\Player;
use App\Models\Tournament;
use App\Services\PlayerService;
use Database\Factories\TournamentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;
use TypeError;

class PlayerCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_success()
    {
        $tournament = Tournament::factory()->create();
        $player = Player::factory()
            ->gender(Gender::from($tournament->gender))
            ->make();
        $player->setTournament($tournament);
        $player->save();

        $this->assertTrue($player->exists);
    }

    public function test_created_has_handicap()
    {
        $tournament = Tournament::factory()->create();
        $player = Player::factory()
            ->gender(Gender::from($tournament->gender))
            ->make();
        $player->setTournament($tournament);
        $player->save();

        $this->assertNotNull($player->handicap);
    }

    public function test_factory_creation()
    {
        $totalPlayers = 2 ** rand(1, 5);
        $tournament = Tournament::factory()
            ->withPlayers(Gender::random(), $totalPlayers)
            ->create();

        $this->assertTrue($tournament->exists);
        $this->assertTrue($tournament->players->count() === $totalPlayers);
    }
}
