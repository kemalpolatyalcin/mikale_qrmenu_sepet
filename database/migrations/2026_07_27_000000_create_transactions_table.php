<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('restaurant_id')->nullable();
                $table->string('table_name')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('payment_method')->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
