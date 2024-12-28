<?php

namespace Tests\Unit\Models;

use App\Enums\Gender;
use App\Exceptions\GenderNotSameException;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;
use TypeError;

class PlayerMakeTest extends TestCase
{
    use RefreshDatabase;

    public function test_gender_accepts_enum_gender_and_string()
    {
        $this->expectNotToPerformAssertions();

        $player = new Player();
        $player->gender = Gender::Male;

        $playerError = new Player();
        $playerError->gender = Gender::Male->value;
    }

    public function test_gender_returns_string_gender()
    {
        $player = new Player();
        $player->gender = Gender::Male;
        $this->assertIsString($player->gender);
    }

    static public function providePlayerAttributes(): array
    {
        return [
            'skill' => ['skill'],
            'strength' => ['strength'],
            'speed' => ['speed'],
            'latency' => ['latency'],
        ];
    }

    /**
     * @dataProvider providePlayerAttributes
     */
    public function test_set_get_player_attributes(string $attribute)
    {
        $player = new Player();
        $config = config('tournament.player_units');
        $value = rand($config[$attribute]['min'] * 100, $config[$attribute]['max'] * 100) / 100;
        $player->$attribute = $value;
        $this->assertEquals($value, $player->$attribute);
    }

    /**
     * @dataProvider providePlayerAttributes
     */
    public function test_player_set_attributes_must_respect_max_range(string $attribute)
    {
        $this->expectException(InvalidArgumentException::class);
        $player = new Player();
        $config = config('tournament.player_units');
        $value = $config[$attribute]['max'] + 1;
        $player->$attribute = $value;
    }

    /**
     * @dataProvider providePlayerAttributes
     */
    public function test_player_set_attributes_must_respect_min_range(string $attribute)
    {
        $this->expectException(InvalidArgumentException::class);
        $player = new Player();
        $config = config('tournament.player_units');
        $value = $config[$attribute]['min'] - 1;
        $player->$attribute = $value;
    }

    public function test_set_tournament()
    {
        $tournament = new Tournament();
        $tournament->gender = Gender::random();
        $tournament->id = 1;
        $player = new Player();
        $player->gender = $tournament->gender;
        $player->setTournament($tournament);
        $this->assertEquals($tournament->id, $player->tournament->id);
    }

    public function test_set_tournament_gender_must_match_player()
    {
        $this->expectException(GenderNotSameException::class);
        $tournament = new Tournament();
        $tournament->gender = Gender::Male;
        $tournament->id = 1;
        $player = new Player();
        $player->gender = Gender::Female;
        $player->setTournament($tournament);
    }
}
