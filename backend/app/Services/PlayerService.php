<?php

namespace App\Services;

use App\Enums\Gender;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Support\Facades\Log;

class PlayerService
{
    protected $maleAttributes = [
        'skill',
        'strength',
        'speed',
    ];

    protected $femaleAttributes = [
        'skill',
        'latency',
    ];

    public function calculateHandicap(Player $player): float
    {
        $handicap = $this->calculateHandicapWithWeights(
            $player,
            Gender::Male ? $this->maleAttributes : $this->femaleAttributes
        );

        Log::info('Player handicap calculated', [
            'handicap' => $handicap,
        ]);

        return $handicap;
    }

    protected function calculateHandicapWithWeights(Player $player, array $attributes): float
    {
        $config = config('tournament.player_units');
        $weightTotal = 0;
        return collect($attributes)
            ->sum(function ($attr) use ($config, &$weightTotal, $player) {
                $defaultMiddleRangeValue = $this->middleInRange($config[$attr]['min'], $config[$attr]['max']);
                $weight = $config[$attr]['handicap_weight'];
                $weightTotal += $weight;
                $value = $player->$attr ?? $defaultMiddleRangeValue;
                return $this->calculateHandicapByAttribute($attr, $value) * $weight;
            }) / $weightTotal;
    }

    protected function calculateHandicapByAttribute(string $attr, float $value): float
    {
        $config = config("tournament.player_units.$attr");
        $percent = $config['handicap_inverse'] ?
            $this->getPercentInRangeInverse($value, $config['min'], $config['max'])
            : $this->getPercentInRange($value, $config['min'], $config['max']);
        return $percent;
    }

    protected function getPercentInRange(float $value, float $min, float $max): float
    {
        return 100 * ($value - $min) / ($max - $min);
    }

    protected function getPercentInRangeInverse(float $value, float $min, float $max): float
    {
        return 100 * ($value - $max) / ($min - $max);
    }

    protected function middleInRange(float $min, float $max): float
    {
        return ($max - $min) / 2 + $min;
    }
}
