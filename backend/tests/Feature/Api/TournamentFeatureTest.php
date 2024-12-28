<?php

namespace Tests\Feature\Api;

use App\Enums\Gender;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\User;
use App\Notifications\Registration\EmailValidated;
use App\Notifications\Registration\EmailValidationPending;
use App\Services\TournamentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TournamentFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    protected function _getCreationValidInput()
    {
        $gender = Gender::random()->value;
        $name = $this->faker->name($gender);
        $players = Player::factory()->gender(Gender::from($gender))->count(10)->make()->toArray();
        return compact('name', 'gender', 'players');
    }

    public function test_create_success()
    {
        $input = $this->_getCreationValidInput();
        $response = $this->post(URL::route('tournament.create'), $input);
        $response->assertCreated();

        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('name', $input['name'])
                ->where('gender', $input['gender'])
                ->has('players', 10)
                ->etc()
        );
    }

    public function test_play_gets_a_winner()
    {
        $tournament = Tournament::factory()->withPlayers(Gender::random(), 2 ** 3)->create();

        $response = $this->post(URL::route('tournament.play', $tournament->id));
        $response->assertOk();

        $tournament->refresh();

        $this->assertNotNull($tournament->winner);
        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('id', $tournament->winner->id)
                ->etc()
        );
    }

    public function test_show_success()
    {
        $tournament = Tournament::factory()->withPlayers(Gender::random(), 2 ** 3)->create();

        $response = $this->get(URL::route('tournament.show', $tournament->id));
        $response->assertOk();

        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('id', $tournament->id)
                ->etc()
        );
    }

    public function test_search_without_queries_success()
    {
        $tournaments = Tournament::factory()
            ->count(10)
            ->withPlayers(Gender::random(), 2 ** 3)
            ->create();
        $tournaments->random()->each(
            fn($tournament) => app(TournamentService::class)->play($tournament)
        );

        $response = $this->get(URL::route('tournament.index'));
        $response->assertOk();
        $response->assertJsonCount(10, 'data');
    }
}
