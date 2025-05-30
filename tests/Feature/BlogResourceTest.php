<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BlogResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
   public function authenticated_user_can_access_blog_list()
    {
        // Crear un usuario (puedes agregar un rol si estás usando Spatie)
        $user = User::factory()->create();

        // Asignar rol si estás usando roles
        $user->assignRole('Editor'); // o 'superadmin'

        // Actuar como el usuario
        $this->actingAs($user);

        // Acceder al recurso del panel de Filament (ajusta la ruta si es distinta)
        $response = $this->get('/admin/blogs');

        // Comprobar que responde con HTTP 200
        $response->assertStatus(200);
    }
}
