<?php

namespace App\Exceptions;

use Exception;

class GenderNotSameException extends Exception
{
    protected $message = 'gender not match';
}
