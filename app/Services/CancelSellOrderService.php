<?php

namespace App\Services;

use App\Enums\AssetEnum;

class CancelSellOrderService extends CancelOrderService
{
    public function refund(): void
    {
        $walletService = WalletService::getWallet(AssetEnum::GOLD->value, $this->order->user_id);
        $walletService->increase(
            $this->order->remaining_amount,
            'Cansel order',
            $this->order
        );
    }
}
