<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Asegúrate de que haya al menos un usuario en la base de datos
        $user = \App\Models\User::first() ?: \App\Models\User::factory()->create();

        // Array de datos de ejemplo
        $blogs = [
            [
                'titulo' => 'Introducción a Laravel',
                'slug' => 'introduccion-a-laravel',
                'contenido' => 'Este es un artículo introductorio sobre Laravel, un framework PHP muy popular.',
                'categoria' => 'tecnologia',
                'etiquetas' => ['laravel', 'php', 'framework'],
                'imagen' => 'introduccion-laravel.jpg',
                'user_id' => $user->id,
            ],
            [
                'titulo' => 'El Arte del Renacimiento',
                'slug' => 'arte-del-renacimiento',
                'contenido' => 'Exploramos las obras maestras del Renacimiento y sus artistas más destacados.',
                'categoria' => 'arte',
                'etiquetas' => ['arte', 'renacimiento', 'historia'],
                'imagen' => 'renacimiento.jpg',
                'user_id' => $user->id,
            ],
            [
                'titulo' => 'Avances en Inteligencia Artificial',
                'slug' => 'avances-en-inteligencia-artificial',
                'contenido' => 'Un vistazo a los últimos avances en IA y su impacto en la tecnología.',
                'categoria' => 'tecnologia',
                'etiquetas' => ['ia', 'tecnologia', 'futuro'],
                'imagen' => 'inteligencia-artificial.jpg',
                'user_id' => $user->id,
            ],
        ];

        // Insertar los datos en la tabla blogs
        foreach ($blogs as $blog) {
            Blog::create($blog);
        }
    }
}
