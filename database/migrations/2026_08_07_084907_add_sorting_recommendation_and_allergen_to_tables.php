<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('sort_order')->default(0);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('sort_order')->default(0);
            $table->boolean('is_recommended')->default(false);
            $table->integer('recommended_sort_order')->default(0);
            $table->text('allergen_info')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'is_recommended', 'recommended_sort_order', 'allergen_info']);
        });
    }
};
