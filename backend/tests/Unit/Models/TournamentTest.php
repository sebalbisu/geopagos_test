<?php

namespace Tests\Unit;

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

class TournamentTest extends TestCase
{
    use RefreshDatabase;

    public function test_gender_must_be_enum_gender_or_string()
    {
        $this->expectNotToPerformAssertions();

        $tournament = new Tournament();
        $tournament->gender = Gender::Male;

        $tournamentError = new Tournament();
        $tournamentError->gender = Gender::Male->value;
    }

    public function test_gender_returns_string()
    {
        $tournament = new Tournament();
        $tournament->gender = Gender::Male;
        $this->assertIsString($tournament->gender);
    }

    public function test_areNumberOfPlayersOk_success()
    {
        $tournament = Tournament::factory()
            ->withPlayers(Gender::random(), 2 ** rand(1, 4))
            ->create();

        $this->assertTrue($tournament->areNumberOfPlayersOk());
    }
}
