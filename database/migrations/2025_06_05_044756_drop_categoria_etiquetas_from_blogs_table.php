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
        Schema::table('blogs', function (Blueprint $table) {
            Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['categoria', 'etiquetas']);
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            Schema::table('blogs', function (Blueprint $table) {
            $table->string('categoria')->nullable();
            $table->json('etiquetas')->nullable();
        });
        });
    }
};
