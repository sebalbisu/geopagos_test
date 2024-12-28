<?php

namespace App\Exceptions;

class TournamentException extends \Exception
{
    public function __construct(string $message = 'Tournament exception', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
