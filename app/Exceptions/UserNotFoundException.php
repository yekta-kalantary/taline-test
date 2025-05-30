<?php

namespace App\Exceptions;

use RuntimeException;

class UserNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("User with ID {$id} not found.");
    }
}
