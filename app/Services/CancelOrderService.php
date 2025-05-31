<?php

namespace App\Services;

use App\Contracts\CancelOrderHandlerInterface;
use App\Enums\OrderStatusEnum;
use App\Models\Order;

abstract class CancelOrderService implements CancelOrderHandlerInterface
{
    public function __construct(protected Order $order) {}

    abstract protected function refund();

    public function cancel(): Order
    {
        $this->refund();
        if ($this->order->remaining_amount != $this->order->amount) {
            $this->order->status = OrderStatusEnum::PARTIALLY_CANCELLED;
        } else {
            $this->order->status = OrderStatusEnum::CANCELLED;
        }
        $this->order->save();

        return $this->order->refresh();
    }
}
