<?php

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function random(): self
    {
        $randomKey = array_rand(self::cases());
        return self::cases()[$randomKey];
    }
}
