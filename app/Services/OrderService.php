<?php

namespace App\Services;

use App\Contracts\OrderHandlerInterface;
use App\Enums\AssetEnum;
use App\Enums\OrderStatusEnum;
use App\Exceptions\AuthenticationRequiredException;
use App\Models\Order;
use App\Models\User;

abstract class OrderService implements OrderHandlerInterface
{
    public const MIN_TRANSACTION_FEE = 500000;

    public const MAX_TRANSACTION_FEE = 50000000;

    protected User $user;


    protected WalletService $goldWallet;

    protected WalletService $rialWallet;

    protected string $orderType;

    public function __construct(
        protected readonly float $goldWeightInGrams,
        protected readonly int   $pricePerGram
    )
    {
        $this->user = auth()->user()
            ?: throw new AuthenticationRequiredException;
        $this->goldWallet = WalletService::getWallet(AssetEnum::GOLD->value);
        $this->rialWallet = WalletService::getWallet(AssetEnum::RIAL->value);
    }

    public function getTotalTransactionAmount(): int
    {
        return (int)round($this->goldWeightInGrams * $this->pricePerGram);
    }

    protected function getFeeRateByWeight(): float
    {
        return match (true) {
            $this->goldWeightInGrams <= 1 => 2.0,
            $this->goldWeightInGrams <= 10 => 1.5,
            default => 1.0,
        };
    }

    public function getFinalFeeAmount(): int
    {
        $raw = $this->getTotalTransactionAmount() * ($this->getFeeRateByWeight() / 100);

        return (int)min(
            max(round($raw), self::MIN_TRANSACTION_FEE),
            self::MAX_TRANSACTION_FEE
        );
    }


    abstract public function hasSufficientBalance();

    abstract public function place(): Order;
}
