<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('table_number')->nullable();
                $table->decimal('total_amount', 10, 2)->nullable();
                $table->boolean('cutlery_requested')->default(false);
                $table->string('payment_method', 50)->default('cash');
                $table->string('coupon_code')->nullable();
                $table->text('order_note')->nullable();
                $table->string('status', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
