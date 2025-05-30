<?php

namespace App\Services;

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Order;

class BuyOrderService extends OrderService
{
    public function __construct(float $goldWeightInGrams, int $unitPricePerGram)
    {
        $this->orderType = OrderTypeEnum::BUY->value;
        parent::__construct($goldWeightInGrams, $unitPricePerGram);
    }

    public function hasSufficientBalance(): void
    {
        $amount = $this->getTotalTransactionAmount() + $this->getFinalFeeAmount();
        $this->rialWallet->ensureSufficientFunds($amount);
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
        $this->rialWallet->decrease($this->getTotalTransactionAmount(), description: 'Buy order payment', transactionable: $order);
        $this->rialWallet->decrease($this->getFinalFeeAmount(), description: 'Buy fee', transactionable: $order);

        return $order;
    }
}
