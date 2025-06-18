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
       $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        }

        Schema::create('categories_temp', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::statement('INSERT INTO categories_temp (id, name, created_at, updated_at) SELECT id, name, created_at, updated_at FROM categories');
        Schema::drop('categories');
        Schema::rename('categories_temp', 'categories');

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        }

        Schema::create('categories_temp', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->after('name');
            $table->timestamps();
        });

        DB::statement('INSERT INTO categories_temp (id, name, created_at, updated_at) SELECT id, name, created_at, updated_at FROM categories');
        Schema::drop('categories');
        Schema::rename('categories_temp', 'categories');

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }
};
