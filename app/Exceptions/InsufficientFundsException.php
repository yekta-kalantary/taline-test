<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientFundsException extends RuntimeException
{
    public function __construct(float $current, float $attempted)
    {
        $attempted = abs($attempted);
        parent::__construct(
            "Insufficient funds: current balance={$current}, attempted deduction={$attempted}."
        );
    }
}
