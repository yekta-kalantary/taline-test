<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'price',
        'amount',
        'remaining_amount',
        'fee_amount',
        'fee_rate',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => OrderTypeEnum::class,
            'status' => OrderStatusEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
