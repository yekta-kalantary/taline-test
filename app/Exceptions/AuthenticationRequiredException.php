<?php

namespace App\Exceptions;

use RuntimeException;

class AuthenticationRequiredException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Authentication required: no user context available.');
    }
}
