<?php

namespace App\Contracts;

use App\Models\Order;

interface CancelOrderHandlerInterface
{
    public function cancel(): Order;
}
