<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'table_number')) {
                $table->string('table_number')->default('Bilinmiyor')->after('id');
            }
            if (!Schema::hasColumn('orders', 'cutlery_requested')) {
                $table->boolean('cutlery_requested')->default(false)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('cutlery_requested');
            }
            if (!Schema::hasColumn('orders', 'coupon_code')) {
                $table->string('coupon_code')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'order_note')) {
                $table->text('order_note')->nullable()->after('coupon_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('orders', 'table_number') ? 'table_number' : null,
                Schema::hasColumn('orders', 'cutlery_requested') ? 'cutlery_requested' : null,
                Schema::hasColumn('orders', 'payment_method') ? 'payment_method' : null,
                Schema::hasColumn('orders', 'coupon_code') ? 'coupon_code' : null,
                Schema::hasColumn('orders', 'order_note') ? 'order_note' : null,
            ]));
        });
    }
};
