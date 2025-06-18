<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        Blog::factory()
            ->count(100) // Crea 10 blogs falsos
            ->hasCategories(3) // Asocia 3 categorías por blog
            ->hasTags(5) // Asocia 5 etiquetas por blog
            ->create();
    }
}
