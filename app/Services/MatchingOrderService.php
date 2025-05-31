<?php

namespace App\Services;

use App\Contracts\MatchingOrderHandlerInterface;
use App\Enums\OrderStatusEnum;
use App\Jobs\OrderMatchingJob;
use App\Models\Order;
use App\Models\Trade;

abstract class MatchingOrderService implements MatchingOrderHandlerInterface
{
    protected float $amount;

    protected int $price;

    protected Order $buyerOrder;

    protected Order $sellerOrder;

    public function __construct(public Order $order) {}

    abstract public function findMatch();

    public function calculateAmount()
    {
        if ($this->buyerOrder->remaining_amount > $this->sellerOrder->remaining_amount) {
            $this->amount = $this->sellerOrder->remaining_amount;
        } elseif ($this->sellerOrder->remaining_amount > $this->buyerOrder->remaining_amount) {
            $this->amount = $this->buyerOrder->remaining_amount;
        } else {
            $this->amount = $this->order->remaining_amount;
        }
    }

    public function setNewRemainingAmount()
    {
        $this->sellerOrder->remaining_amount = $this->sellerOrder->remaining_amount - $this->amount;
        if ($this->sellerOrder->remaining_amount > 0) {
            $this->sellerOrder->status = OrderStatusEnum::PARTIALLY_FILLED;
        } else {
            $this->sellerOrder->status = OrderStatusEnum::FILLED;
        }
        $this->sellerOrder->save();

        $this->buyerOrder->remaining_amount = $this->buyerOrder->remaining_amount - $this->amount;
        if ($this->buyerOrder->remaining_amount > 0) {
            $this->buyerOrder->status = OrderStatusEnum::PARTIALLY_FILLED;
        } else {
            $this->buyerOrder->status = OrderStatusEnum::FILLED;
        }
        $this->buyerOrder->save();
    }

    abstract public function walletTransfer();

    public function execute()
    {
        $hasMatch = $this->findMatch();
        if (! $hasMatch) {
            return;
        }
        $this->calculateAmount();
        $this->setNewRemainingAmount();
        $this->walletTransfer();
        Trade::create([
            'buyer_order_id' => $this->buyerOrder->id,
            'seller_order_id' => $this->sellerOrder->id,
            'price' => $this->price,
            'amount' => $this->amount,
        ]);
        $order = $this->order->refresh();
        if ($order->remaining_amount != 0.0) {
            OrderMatchingJob::dispatch($order)->onQueue('high');
        }
    }
}
