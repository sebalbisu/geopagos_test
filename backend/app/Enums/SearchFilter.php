<?php

namespace App\Enums;

enum SearchFilter: string
{
    case Name = 'name';
    case StartedAt = 'date';
    case Since = 'since';
    case Until = 'until';
    case Gender = 'gender';
}
