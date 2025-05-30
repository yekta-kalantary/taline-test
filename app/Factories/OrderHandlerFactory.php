<?php

namespace App\Factories;

use App\Contracts\OrderHandlerInterface;
use App\Enums\OrderTypeEnum;
use App\Exceptions\OrderTypeNotSupportedException;
use App\Services\BuyOrderService;
use App\Services\SellOrderService;

class OrderHandlerFactory
{
    public static function make(string $type, float $gold, int $price): OrderHandlerInterface
    {
        return match ($type) {
            OrderTypeEnum::BUY->value => new BuyOrderService($gold, $price),
            OrderTypeEnum::SELL->value => new SellOrderService($gold, $price),
            default => throw new OrderTypeNotSupportedException($type),
        };
    }
}
