<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        // Array de etiquetas de ejemplo
        $tags = [
            ['name' => 'Laravel'],
        
            ['name' => 'Framework'],
            ['name' => 'Arte'],
            ['name' => 'Renacimiento'],
            ['name' => 'Historia'],
            ['name' => 'IA'],
            ['name' => 'Tecnologia'],
            ['name' => 'Futuro'],
        ];

        // Insertar las etiquetas en la tabla
        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}