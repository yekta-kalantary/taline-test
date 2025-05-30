<?php

namespace App\Exceptions;

use InvalidArgumentException;

class OrderTypeNotSupportedException extends InvalidArgumentException
{
    public function __construct(string $type)
    {
        parent::__construct("Order type “{$type}” is not supported.");
    }
}
