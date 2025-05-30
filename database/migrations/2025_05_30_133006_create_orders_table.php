<?php

use App\Models\User;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained();
            $table->string('type')->comment('نوع سفارش');
            $table->unsignedBigInteger('price')->comment('ریال');
            $table->decimal('amount', 16, 3)->comment('مقدار طلا');
            $table->decimal('remaining_amount', 16, 3)->comment('مانده طلا برای حالت خرید پارشیالی');
            $table->unsignedInteger('fee_amount');
            $table->unsignedInteger('fee_rate');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
