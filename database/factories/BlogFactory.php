<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition()
    {
        $titulo = $this->faker->sentence(6); // Genera un título de 6 palabras
        return [
            'titulo' => $titulo,
            'slug' => Str::slug($titulo),
            'contenido' => $this->faker->paragraphs(3, true), // 3 párrafos de texto
            'user_id' => User::factory(), // Crea un usuario falso o usa uno existente
            'imagen' => 'blog_images/' . $this->faker->image('public/storage/blog_images', 640, 480, null, false), // Genera una imagen falsa
        ];
    }
}