<?php

namespace App\Services;

use App\Enums\AssetEnum;
use App\Exceptions\AssetNotSupportedException;
use App\Exceptions\AuthenticationRequiredException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WalletService
{
    protected Wallet $wallet;

    protected int $ttl = 300;

    protected function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public static function getWallet(AssetEnum|string $asset, User|int|null $user = null): self
    {
        $assetEnum = $asset instanceof AssetEnum
            ? $asset
            : AssetEnum::tryFrom(strtolower((string) $asset));

        if (! $assetEnum) {
            throw new AssetNotSupportedException((string) $asset);
        }

        if ($user instanceof User) {
            if (! $user->exists || is_null($user->id)) {
                throw new UserNotFoundException($user->id ?? 0);
            }
            $userModel = User::find($user->id)
                ?: throw new UserNotFoundException($user->id);
        } elseif (is_int($user)) {
            $userModel = User::find($user)
                ?: throw new UserNotFoundException($user);
        } else {
            $auth = Auth::user();
            if (! $auth) {
                throw new AuthenticationRequiredException;
            }
            $userModel = $auth;
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userModel->id, 'asset' => $assetEnum->value],
            ['balance' => 0]
        );

        return new self($wallet);
    }

    public function ensureSufficientFunds(float $amount, bool $force = false): void
    {
        if ($force) {
            return;
        }
        $current = $this->wallet->refresh()->balance;
        if ($amount > $current) {
            throw new InsufficientFundsException($current, $amount);
        }
    }

    private function addTransaction(
        float $amount,
        bool $force = false,
        ?string $description = null,
        ?\Illuminate\Database\Eloquent\Model $transactionable = null
    ): self {
        DB::transaction(function () use ($amount, $force, $description, $transactionable) {
            $this->ensureSufficientFunds($amount, $force);
            $data = ['amount' => $amount, 'description' => $description];
            if ($transactionable) {
                $data['transactionable_type'] = get_class($transactionable);
                $data['transactionable_id'] = $transactionable->getKey();
            }
            $this->wallet->transactions()->create($data);
            $this->wallet->balance += $amount;
            $this->wallet->save();
        });

        Cache::forget($this->cacheKeyBalance());
        for ($p = 1; $p <= 5; $p++) {
            Cache::forget($this->cacheKeyHistory($p, 15));
        }

        return $this;
    }

    public function increase(
        float $amount,
        ?string $description = null,
        ?\Illuminate\Database\Eloquent\Model $transactionable = null
    ): self {
        return $this->addTransaction(abs($amount), true, $description, $transactionable);
    }

    public function decrease(
        float $amount,
        bool $force = false,
        ?string $description = null,
        ?\Illuminate\Database\Eloquent\Model $transactionable = null
    ): self {
        return $this->addTransaction(-abs($amount), $force, $description, $transactionable);
    }

    public function getBalance(): float
    {
        return Cache::remember(
            $this->cacheKeyBalance(),
            $this->ttl,
            fn () => (float) $this->wallet->refresh()->balance
        );
    }

    public function history(int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        return Cache::remember(
            $this->cacheKeyHistory($page, $perPage),
            $this->ttl,
            fn () => $this->wallet
                ->transactions()
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page)
        );
    }

    private function cacheKeyBalance(): string
    {
        return "wallet:{$this->wallet->id}:balance";
    }

    private function cacheKeyHistory(int $page, int $perPage): string
    {
        return "wallet:{$this->wallet->id}:history:page={$page}:per={$perPage}";
    }
}
