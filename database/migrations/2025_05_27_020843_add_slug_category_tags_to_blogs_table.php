<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('slug')->unique()->after('titulo');
            $table->string('categoria')->nullable()->after('slug');
            $table->json('etiquetas')->nullable()->after('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['slug', 'categoria', 'etiquetas']);
        });
    }
};
