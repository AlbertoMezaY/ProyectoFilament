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
        Schema::rename('tag_blog', 'blog_tag');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('blog_tag', 'tag_blog');
    }
};
