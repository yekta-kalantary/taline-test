<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
