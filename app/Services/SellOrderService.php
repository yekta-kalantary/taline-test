<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Order;

class SellOrderService extends OrderService
{
    public function __construct(float $goldWeightInGrams, int $unitPricePerGram)
    {
        $this->orderType = OrderTypeEnum::SELL->value;
        parent::__construct($goldWeightInGrams, $unitPricePerGram);
    }

    public function hasSufficientBalance(): void
    {
        $this->goldWallet->ensureSufficientFunds($this->goldWeightInGrams);
        $this->rialWallet->ensureSufficientFunds($this->getFinalFeeAmount());
    }

    public function place(): Order
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'type' => $this->orderType,
            'price' => $this->pricePerGram,
            'amount' => $this->goldWeightInGrams,
            'remaining_amount' => $this->goldWeightInGrams,
            'fee_amount' => $this->getFinalFeeAmount(),
            'fee_rate' => $this->getFeeRateByWeight(),
            'status' => OrderStatusEnum::PENDING->value,
        ]);

        $this->goldWallet->decrease($this->goldWeightInGrams, description: 'Gold sold', transactionable: $order);
        $this->rialWallet->decrease($this->getFinalFeeAmount(), description: 'Sale fee', transactionable: $order);

        return $order;
    }
}
