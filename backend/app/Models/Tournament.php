<?php

namespace App\Models;

use App\Enums\Gender;
use App\Exceptions\GenderNotMatchException;
use App\Exceptions\GenderNotSameException;
use App\Services\TournamentService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Traversable;

/**
 * 
 *
 * @property int $id
 * @property string $gender
 * @property string|null $name
 * @property string $start_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game> $games
 * @property-read int|null $games_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Player> $players
 * @property-read int|null $players_count
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereUpdatedAt($value)
 * @property string $started_at
 * @property int $winner_id
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tournament whereWinnerId($value)
 * @property-read \App\Models\Player $winner
 * @method static \Database\Factories\TournamentFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'name',
        'started_at'
    ];

    protected $dates = [
        'started_at',
        'created_at',
        'updated_at',
    ];

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function gender(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value instanceof Gender ? $value->value : $value,
        );
    }

    /**
     * @param array<Player> $players
     * @return void
     */
    public function addPlayers(iterable $players): void
    {
        DB::transaction(function () use ($players) {
            foreach ($players as $player) {
                $player->setTournament($this);
                $player->save();
            }
            $this->load('players');
        });
    }

    public function areNumberOfPlayersOk(): bool
    {
        if (count($this->players) < 2) {
            return false;
        }
        if (!$this->isPowerOfTwo(count($this->players))) {
            return false;
        }
        return true;
    }

    static public function isPowerOfTwo(int $number): bool
    {
        return ($number & ($number - 1)) == 0;
    }
}
