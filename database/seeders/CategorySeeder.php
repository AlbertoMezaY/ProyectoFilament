<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Array de categorías de ejemplo
        $categories = [
            ['name' => 'Tecnologia'],
            ['name' => 'Arte'],
            ['name' => 'Ciencia'],
        ];

        // Insertar las categorías en la tabla
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}