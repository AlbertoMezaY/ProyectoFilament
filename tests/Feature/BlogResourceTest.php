<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Livewire\Livewire;

use Spatie\Permission\Models\Permission; // Importa la clase Permission también

class BlogResourceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Usa el nombre simplificado 'Role'
        $superAdmin = Role::findOrCreate('super_admin');
        $permissions = [
            'view_any_blog',
            'view_blog',
            'create_blog',
            'update_blog',
            'delete_blog',
        ];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission); // Usa 'Permission' simplificado
        }
        $superAdmin->givePermissionTo($permissions);
    }


    
#[Test]
public function editor_can_create_a_blog()
{
   Storage::fake('public');
    $user = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($user);

    $category = Category::factory()->create();
    $tag = Tag::factory()->create();

    Livewire::actingAs($user)
        ->test(\App\Filament\Resources\BlogResource\Pages\CreateBlog::class)
        ->set('data.titulo', 'Mi primer blog')
        ->set('data.contenido', 'Contenido de ejemplo')
        ->set('data.user_id', $user->id)
        ->set('data.categories', [$category->id])
        ->set('data.tags', [$tag->id])
        ->set('data.imagen', UploadedFile::fake()->image('blog.jpg'))
        ->call('create')
        ->assertRedirect('/admin/blogs');

    $this->assertDatabaseHas('blogs', ['titulo' => 'Mi primer blog']);
}


#[Test]
public function blog_title_is_required()
{
    $user = User::factory()->create()->assignRole('super_admin');
      $this->actingAs($user);

      Livewire::actingAs($user)
          ->test(\App\Filament\Resources\BlogResource\Pages\CreateBlog::class)
          ->set('data.titulo', '')
          ->set('data.contenido', 'Contenido sin título')
          ->set('data.user_id', $user->id)
          ->call('create')
          ->assertHasErrors(['data.titulo' => 'required']);

      $this->assertDatabaseMissing('blogs', ['titulo' => '']);
}

    #[Test]
    public function unauthorized_user_cannot_access_blog()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/blogs');
        $response->assertForbidden();
    }

   #[Test]
public function editor_can_update_a_blog()
{
    Storage::fake('public');
    $user = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($user);

    $category = Category::factory()->create();
    $tag = Tag::factory()->create();

    $blog = Blog::factory()->create([
        'user_id' => $user->id,
        'titulo' => 'Blog original',
        'contenido' => 'Contenido original',
    ]);

    $blog->categories()->attach($category->id);
    $blog->tags()->attach($tag->id);

    $newTitle = 'Blog actualizado';

    Livewire::actingAs($user)
        ->test(\App\Filament\Resources\BlogResource\Pages\EditBlog::class, ['record' => $blog->id])
        ->set('data.titulo', $newTitle)
        ->set('data.contenido', 'Contenido actualizado')
        ->set('data.user_id', $user->id)
        ->set('data.categories', [$category->id])
        ->set('data.tags', [$tag->id])
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('blogs', ['titulo' => $newTitle]);
}


#[Test]
public function editor_can_delete_a_blog_from_list()
{
    $user = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($user);

    $blog = Blog::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(\App\Filament\Resources\BlogResource\Pages\ListBlogs::class)
        ->callTableAction('delete', $blog);

    $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
}
}