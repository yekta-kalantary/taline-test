<?php

namespace App\Services;

use App\Enums\AssetEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Order;
use Log;

class MatchingSellOrderService extends MatchingOrderService
{
    public function __construct(public Order $order)
    {

        $this->sellerOrder = $order;
        $this->price = $order->price;
        parent::__construct($order);
    }

    public function findMatch()
    {
        $buyOrder = Order::query()
            ->where('type', OrderTypeEnum::BUY)
            ->where('price', $this->sellerOrder->price)
            ->where('id', '<', $this->sellerOrder->id)
            ->whereIn('status', [
                OrderStatusEnum::PENDING,
                OrderStatusEnum::PARTIALLY_FILLED,
            ])
            ->orderBy('id', 'asc')
            ->first();
        Log::error('sell: '.var_export($buyOrder, true));
        if (! $buyOrder) {
            return false;
        }
        $this->buyerOrder = $buyOrder;

        return true;
    }

    public function walletTransfer()
    {
        WalletService::getWallet(AssetEnum::GOLD, $this->buyerOrder->user_id)
            ->increase($this->amount, 'PARTIALLY FILLED', $this->buyerOrder);
    }
}
