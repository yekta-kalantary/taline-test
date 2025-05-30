<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(float $current, float $attempted)
    {
        parent::__construct(
            "Insufficient balance: current={$current}, attempted deduction=".abs($attempted)
        );
    }
}
