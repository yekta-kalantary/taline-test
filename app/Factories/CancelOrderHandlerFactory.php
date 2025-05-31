<?php

namespace App\Factories;

use App\Contracts\CancelOrderHandlerInterface;
use App\Enums\OrderTypeEnum;
use App\Models\Order;
use App\Services\CancelBuyOrderService;
use App\Services\CancelSellOrderService;

class CancelOrderHandlerFactory
{
    public static function make(Order $order): CancelOrderHandlerInterface
    {
        return match (true) {
            $order->type == OrderTypeEnum::BUY => new CancelBuyOrderService($order),
            $order->type == OrderTypeEnum::SELL => new CancelSellOrderService($order),
        };
    }
}
