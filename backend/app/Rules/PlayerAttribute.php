<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PlayerAttribute implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $name, mixed $value, Closure $fail): void
    {
        $range = config("tournament.player_units.$name");
        if ($value < $range['min'] || $value > $range['max']) {
            $fail("$name must be between {$range['min']} and {$range['max']}");
        }
    }
}
