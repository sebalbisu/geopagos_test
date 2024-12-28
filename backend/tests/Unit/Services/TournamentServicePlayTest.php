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
use InvalidArgumentException;
use Tests\TestCase;
use TypeError;

class TournamentServicePlayTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_play_with_less_than_2_players()
    {
        $this->expectExceptionObject(new TournamentException('bad number of players'));

        $tournament = Tournament::factory()
            ->withPlayers(Gender::random(), 1)
            ->create();

        app(TournamentService::class)->play($tournament);
    }

    public function test_cannot_play_without_players_quantity_power_of_2()
    {
        $this->expectExceptionObject(new TournamentException('bad number of players'));

        do {
            $number = rand(1, 30);
        } while (Tournament::isPowerOfTwo($number));

        $tournament = Tournament::factory()
            ->withPlayers(Gender::random(), $number)
            ->create();

        app(TournamentService::class)->play($tournament);
    }

    public function test_play()
    {
        $tournament = Tournament::factory()
            ->withPlayers(Gender::random(), 2 ** rand(1, 3))
            ->create();

        $winner = app(TournamentService::class)->play($tournament);

        $this->assertNotNull($tournament->winner);
        $this->assertSame($tournament->winner, $winner);
        $this->assertTrue($tournament->players->contains($winner));
    }
}
