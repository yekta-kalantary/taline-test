<?php

namespace App\Services;

use App\Enums\AssetEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Order;
use Log;

class MatchingBuyOrderService extends MatchingOrderService
{
    public function __construct(public Order $order)
    {

        $this->buyerOrder = $order;
        $this->price = $order->price;
        parent::__construct($order);
    }

    public function findMatch()
    {
        $sellOrder = Order::query()
            ->where('type', OrderTypeEnum::SELL)
            ->where('price', $this->buyerOrder->price)
            ->where('id', '<', $this->buyerOrder->id)
            ->whereIn('status', [
                OrderStatusEnum::PENDING,
                OrderStatusEnum::PARTIALLY_FILLED,
            ])
            ->orderBy('id', 'asc')
            ->first();
        if (! $sellOrder) {
            return false;
        }
        $this->sellerOrder = $sellOrder;
        Log::error('buy: '.var_export($sellOrder, true));

        return true;
    }

    public function walletTransfer()
    {
        WalletService::getWallet(AssetEnum::GOLD, $this->sellerOrder->user_id)
            ->increase($this->amount, 'PARTIALLY FILLED', $this->sellerOrder);
    }
}
