<?php

namespace Tests\Unit\Services;

use App\Enums\Gender;
use App\Exceptions\GenderNotSameException;
use App\Models\Game;
use App\Models\Player;
use App\Models\Tournament;
use App\Services\GameService;
use App\Services\PlayerService;
use Database\Factories\TournamentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;
use TypeError;

class GameServicePlayTest extends TestCase
{
    use RefreshDatabase;

    public function test_play_has_a_winner()
    {
        $tournament = Tournament::factory()
            ->withPlayers(Gender::random(), 2 ** 4)
            ->create();
        $players = $tournament->players->chunk(2)->first();

        $game = app(GameService::class)->create($players[0], $players[1], $stage = 1);
        app(GameService::class)->play($game);

        $this->assertNotNull($game->winner);
        $this->assertTrue($game->winner === $players[0] || $game->winner === $players[1]);
    }
}
