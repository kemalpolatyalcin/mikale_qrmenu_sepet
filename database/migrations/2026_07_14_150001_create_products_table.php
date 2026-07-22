<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('name', 100);
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->integer('calories')->nullable()->default(0);
                $table->integer('prep_time')->nullable()->default(15);
                $table->boolean('is_gluten_free')->default(false);
                $table->string('image_url', 255)->nullable()->default('');
                $table->timestamps();
            });

            DB::table('products')->insert([
                ['id' => 1, 'category_id' => 1, 'name' => 'Izgarada Çıtır Tavuk', 'description' => 'Özel soslu ızgara çıtır tavuk dilimleri', 'price' => 250.00, 'calories' => 288, 'prep_time' => 15, 'is_gluten_free' => 1, 'image_url' => 'images/izgara.jpg'],
                ['id' => 2, 'category_id' => 2, 'name' => 'Full-House Mix Pizza', 'description' => 'Mantar, sucuk, salam, sosis, mısır, kaşar, siyah zeytin, biber...', 'price' => 350.00, 'calories' => 350, 'prep_time' => 35, 'is_gluten_free' => 0, 'image_url' => 'images/pizza2.jpeg'],
                ['id' => 3, 'category_id' => 1, 'name' => 'Cheddar Burger', 'description' => 'Cheddar peyniri, hamburger köftesi, marul, domates, ketçap, hardal...', 'price' => 350.00, 'calories' => 300, 'prep_time' => 35, 'is_gluten_free' => 0, 'image_url' => 'images/cheddar.jpg']
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
