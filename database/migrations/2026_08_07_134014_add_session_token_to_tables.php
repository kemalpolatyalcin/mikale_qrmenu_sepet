<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->string('session_token', 64)->nullable();
            $table->timestamp('session_expires_at')->nullable();
        });

        foreach (\App\Models\Table::all() as $t) {
            $t->session_token = Str::random(32);
            $t->session_expires_at = now()->addHours(2);
            $t->save();
        }
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['session_token', 'session_expires_at']);
        });
    }
};
