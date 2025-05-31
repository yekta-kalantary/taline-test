<?php

namespace App\Factories;

use App\Contracts\MatchingOrderHandlerInterface;
use App\Enums\OrderTypeEnum;
use App\Models\Order;
use App\Services\MatchingBuyOrderService;
use App\Services\MatchingSellOrderService;
use Log;

class OrderMatchingHandlerFactory
{
    public static function make(Order $order): MatchingOrderHandlerInterface
    {
        Log::error('factory matching job started');

        return match (true) {
            $order->type == OrderTypeEnum::BUY => new MatchingBuyOrderService($order),
            $order->type == OrderTypeEnum::SELL => new MatchingSellOrderService($order),
        };
    }
}
