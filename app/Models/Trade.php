<?php

namespace App\Models;

use App\Enums\OrderTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::saving(function (self $model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    protected $fillable = [
        'buyer_order_id',
        'seller_order_id',
        'trade_type',
        'price',
        'amount',
    ];

    protected $appends = [
        'price_in_toman',
    ];

    protected function casts(): array
    {
        return [
            'trade_type' => OrderTypeEnum::class,
        ];
    }

    public function getPriceInTomanAttribute(): float|int
    {
        return $this->price / 10;
    }

    public function buyer_order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'buyer_order_id');
    }

    public function seller_order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'seller_order_id');
    }
}
