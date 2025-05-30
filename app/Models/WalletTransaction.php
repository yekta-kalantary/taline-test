<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WalletTransaction extends Model
{
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    protected $fillable = [
        'wallet_id',
        'amount',
        'description',
        'transactionable_type',
        'transactionable_id',
    ];

    protected $casts = [
        'amount' => 'decimal:3',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
