<?php

namespace App\Contracts;

use App\Models\Order;

interface OrderHandlerInterface
{
    public function hasSufficientBalance();

    public function place(): Order;
}
