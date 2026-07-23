<?php

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
        Schema::table('tables', function (Blueprint $table) {
            $table->string('active_session_id', 40)->nullable();
        });

        foreach (\App\Models\Table::all() as $t) {
            $t->active_session_id = \Illuminate\Support\Str::random(40);
            $t->save();
        }
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('active_session_id');
        });
    }
};
