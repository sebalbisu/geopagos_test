<?php

namespace App\Models;

use App\Enums\Gender;
use App\Exceptions\GenderNotSameException;
use App\Rules\PlayerAttribute;
use App\Services\PlayerService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $tournament_id
 * @property int $dni
 * @property string $gender
 * @property string $first_name
 * @property string $last_name
 * @property int $age
 * @property float $skill
 * @property float|null $strength
 * @property float|null $speed
 * @property float|null $latency
 * @property float $handicap
 * @property-read \App\Models\Tournament $tournament
 * @method static \Illuminate\Database\Eloquent\Builder|Player newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Player newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Player query()
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereDni($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereHandicap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereLatency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereSkill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereStrength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereTournamentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereTournamentRanking($value)
 * @method static \Database\Factories\PlayerFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'dni',
        'first_name',
        'last_name',
        'gender',
        'age',
        'skill',
        'strength',
        'speed',
        'latency',
    ];

    static protected function boot(): void
    {
        parent::boot();
        static::creating(function (Player $player) {
            $player->setupHandicap();
        });
    }

    public function gender(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value instanceof Gender ? $value->value : $value,
        );
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function setTournament(Tournament $tournament): self
    {
        if (!$tournament->gender) {
            throw new GenderNotSameException();
        }
        if ($this->gender != $tournament->gender) {
            throw new GenderNotSameException();
        }
        $this->tournament()->associate($tournament);
        return $this;
    }

    public function games()
    {
        return $this->hasMany(Game::class, 'player_1_id')
            ->orWhere('player_2_id', $this->id)
            ->sortBy('id', 'desc');
    }

    public function setHandicap(float $value): self
    {
        $this->handicap = $value;
        return $this;
    }

    public function setupHandicap(): self
    {
        if (is_null($this->handicap)) {
            $handicap = app(PlayerService::class)->calculateHandicap($this);
            $this->setHandicap($handicap);
        }
        return $this;
    }

    public function  skill(): Attribute
    {
        return Attribute::make(
            set: $this->makeSetterForPlayerAttribute('skill'),
        );
    }

    public function  strength(): Attribute
    {
        return Attribute::make(
            set: $this->makeSetterForPlayerAttribute('strength'),
        );
    }

    public function  speed(): Attribute
    {
        return Attribute::make(
            set: $this->makeSetterForPlayerAttribute('speed'),
        );
    }

    public function  latency(): Attribute
    {
        return Attribute::make(
            set: $this->makeSetterForPlayerAttribute('latency'),
        );
    }

    protected function makeSetterForPlayerAttribute(string $name): callable
    {
        return function ($value) use ($name): float {
            $validator = validator([$name => $value], [
                $name => ['required', 'numeric', new PlayerAttribute($name)],
            ]);
            if ($validator->fails()) {
                throw new \InvalidArgumentException("Invalid $name");
            }
            return $value;
        };
    }
}
