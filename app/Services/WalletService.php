<?php
namespace App\Services;

use App\Enums\AssetEnum;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

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
            : AssetEnum::tryFrom(strtoupper($asset));

        if (! $assetEnum) {
            throw new InvalidArgumentException(
                "Asset '{$asset}' is not supported. Allowed: "
                . implode(', ', array_map(fn($e) => $e->value, AssetEnum::cases()))
            );
        }

        if ($user instanceof User) {
            $userId = $user->id;
        } elseif (is_int($user)) {
            $userId = $user;
        } else {
            $authUser = Auth::user();
            if (! $authUser) {
                throw new RuntimeException('User must be provided or authenticated.');
            }
            $userId = $authUser->id;
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userId, 'asset' => $assetEnum->value],
            ['balance' => 0]
        );

        return new self($wallet);
    }

    public function addTransaction(
        float $amount,
        ?string $description = null,
        ?Model $transactionable = null
    ): self {
        DB::transaction(function () use ($amount, $description, $transactionable) {
            $data = ['amount' => $amount, 'description' => $description];
            if ($transactionable) {
                $data['transactionable_type'] = get_class($transactionable);
                $data['transactionable_id']   = $transactionable->getKey();
            }
            $this->wallet->transactions()->create($data);
            $this->wallet->balance += $amount;
            $this->wallet->save();
        });

        Cache::tags($this->cacheTag())->flush();

        return $this;
    }

    public function getBalance(): float
    {
        return Cache::tags($this->cacheTag())
            ->remember($this->cacheTag() . ':balance', $this->ttl, fn() => (float) $this->wallet->refresh()->balance);
    }

    public function history(int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        return Cache::tags($this->cacheTag())
            ->remember(
                $this->cacheTag() . ":transactions:page={$page}:per={$perPage}",
                $this->ttl,
                fn() => $this->wallet
                    ->transactions()
                    ->orderBy('created_at')
                    ->paginate($perPage, ['*'], 'page', $page)
            );
    }

    private function cacheTag(): string
    {
        return "wallet_{$this->wallet->id}";
    }
}
