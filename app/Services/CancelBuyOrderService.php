<?php

namespace App\Services;

use App\Enums\AssetEnum;

class CancelBuyOrderService extends CancelOrderService
{
    public function refund(): void
    {
        $walletService = WalletService::getWallet(AssetEnum::RIAL->value, $this->order->user_id);
        $total = $this->order->remaining_amount * $this->order->price;
        $walletService->increase(
            $total,
            'Cansel order',
            $this->order
        );
    }
}
