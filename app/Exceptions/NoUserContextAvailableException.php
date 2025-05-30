<?php

namespace App\Exceptions;

use RuntimeException;

class NoUserContextAvailableException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Authentication required: no user context available.');
    }
}
