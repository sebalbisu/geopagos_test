<?php

namespace App\Services;

use App\Enums\TournamentStage;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use App\Exceptions\TournamentException;

class GameService
{

    public function create(Player $player1, Player $player2, int $stage = null): Game
    {
        $game = (new Game())
            ->setPlayer1($player1)
            ->setPlayer2($player2)
            ->setTournament($player1->tournament)
            ->setTournamentStage($stage);
        $game->save();
        Log::info('Game created', [
            'id' => $game->id,
            'tournament' => $player1->tournament->id,
            'stage' => $game->tournament_stage,
            'player1' => $player1->id,
            'player2' => $player2->id,
        ]);
        return $game;
    }

    public function play(Game $game): Game
    {
        if ($game->winner) {
            throw new TournamentException('The game has already been played');
        }
        $game->checkPlayerSameGenders();

        $game->started_at = now();

        Log::info('Game started', [
            'game' => $game->id,
            'tournament' => $game->tournament->id,
            'handicap1' => $game->player1->handicap,
            'handicap2' => $game->player2->handicap,
        ]);

        $handicap1 = $game->player1->handicap;
        $handicap2 = $game->player2->handicap;

        while ($handicap1 > 0 && $handicap2 > 0) {
            $turn = rand(1, 2); // random 1 or 2
            $shot = rand(0, 5 * 10) / 10; // random between 0.0 and 5.0 (with one decimal)
            if ($turn == 1) {
                $handicap2 -= $shot;
            } else {
                $handicap1 -= $shot;
            }
        }

        $game->setWinner($handicap1 <= 0 ? $game->player2 : $game->player1);

        $game->save();

        Log::info('Game finished', [
            'tournament' => $game->tournament->id,
            'game' => $game->id,
            'winner' => $game->winner->id,
            'handicap1' => $handicap1,
            'handicap2' => $handicap2,
        ]);

        return $game;
    }
}
