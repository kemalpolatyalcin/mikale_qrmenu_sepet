<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50);
                $table->string('image_url', 255)->default('');
                $table->timestamps();
            });

            DB::table('categories')->insert([
                ['id' => 1, 'name' => 'BAŞLANGIÇ', 'image_url' => 'images/baslangic.jpg'],
                ['id' => 2, 'name' => 'PİZZA', 'image_url' => 'images/pizza.jpg'],
                ['id' => 3, 'name' => 'KEBAP', 'image_url' => 'images/kebap.webp'],
                ['id' => 4, 'name' => 'DÖNER', 'image_url' => 'images/doner.jpg']
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
