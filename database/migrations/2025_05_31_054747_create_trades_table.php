<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class, 'buyer_order_id')->constrained();
            $table->foreignIdFor(Order::class, 'seller_order_id')->constrained();
            $table->unsignedBigInteger('price')->comment('ریال');
            $table->decimal('amount', 16, 3);
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
