<?php

namespace App\Models;

use App\Enums\AssetEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::saving(function (self $model) {
            $model->updated_at = $model->freshTimestamp();
        });
    }

    protected $fillable = [
        'user_id',
        'asset',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'asset' => AssetEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransactionHistory::class, 'wallet_id');
    }
}
