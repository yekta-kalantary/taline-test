<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    /**
     * سفارش ثبت شده
     */
    case PENDING = 'pending';

    /**
     * بخشی از سفارش پارشیالی پر شده
     */
    case PARTIALLY_FILLED = 'partially_filled';

    /**
     * سفارش کامل شد
     */
    case FILLED = 'filled';

    /**
     * سفارش بدون هیچ فروشی کنسل شده
     */
    case CANCELLED = 'cancelled';

    /**
     * بخشی از سفارش کنسل شده
     */
    case PARTIALLY_CANCELLED = 'partially_cancelled';
}
