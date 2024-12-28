<?php

namespace App\Models;

use App\Enums\TournamentStage;
use App\Exceptions\GenderNotSameException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TournamentException;

/**
 * 
 *
 * @property int $id
 * @property int $tournament_id
 * @property int $tournament_stage
 * @property string|null $tournament_stage_name
 * @property int $winner_id
 * @property int $player_1_id
 * @property int $player_2_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Player $player1
 * @property-read \App\Models\Player $player2
 * @property-read \App\Models\Tournament $tournament
 * @property-read \App\Models\Player $winner
 * @method static \Illuminate\Database\Eloquent\Builder|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game wherePlayer1Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game wherePlayer2Id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereTournamentStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereTournamentStageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereWinnerId($value)
 * @property string $started_at
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereStartedAt($value)
 * @method static \Database\Factories\GameFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Game extends Model
{
    use HasFactory;

    protected $dates = [
        'started_at',
        'created_at',
        'updated_at',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function setTournament(Tournament $tournament): self
    {
        $this->tournament()->associate($tournament);
        return $this;
    }

    public function setTournamentStage(int $stage): self
    {
        $this->tournament_stage = $stage;
        return $this;
    }

    public function player1()
    {
        return $this->belongsTo(Player::class, 'player_1_id');
    }

    public function setPlayer1(Player $player): self
    {
        $this->player1()->associate($player);
        return $this;
    }

    public function player2()
    {
        return $this->belongsTo(Player::class, 'player_2_id');
    }

    public function setPlayer2(Player $player): self
    {
        $this->player2()->associate($player);
        return $this;
    }

    public function winner()
    {
        return $this->belongsTo(Player::class, 'winner_id');
    }

    public function setWinner(Player $player): self
    {
        $this->winner()->associate($player);
        return $this;
    }

    public function checkPlayerSameGenders()
    {
        if (!$this->player1->gender) {
            throw new GenderNotSameException();
        }
        if ($this->player1->gender != $this->player2->gender) {
            throw new GenderNotSameException();
        }
    }
}
