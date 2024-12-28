<?php

namespace Tests\Unit\Services;

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

class PlayerServiceHandicapTest extends TestCase
{
    use RefreshDatabase;

    static public function providerGender(): array
    {
        return [
            'male' => [Gender::Male],
            'female' => [Gender::Female],
        ];
    }

    /**
     * @dataProvider providerGender
     */
    public function test_handicap(Gender $gender)
    {
        $player = Player::factory()->gender($gender)->make();
        $handicap = app(PlayerService::class)->calculateHandicap($player);

        $this->assertIsFloat($handicap);
        $this->assertThat(
            $handicap,
            $this->logicalAnd(
                $this->greaterThanOrEqual(0),
                $this->lessThanOrEqual(100)
            )
        );
    }
}
