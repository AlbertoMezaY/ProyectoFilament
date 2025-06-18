<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class CategoryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $superAdmin = Role::findOrCreate('super_admin');
        $permissions = [
            'view_any_category',
            'view_category',
            'create_category',
            'update_category',
            'delete_category',
        ];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }
        $superAdmin->givePermissionTo($permissions);
    }

    #[Test]
public function super_admin_can_create_a_category()
{
    $user = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($user);

    Livewire::actingAs($user)
        ->test(\App\Filament\Resources\CategoryResource\Pages\CreateCategory::class)
        ->set('data.name', 'Nueva Categoría')
        ->call('create')
        ->assertRedirect('/admin/categories/' . Category::latest()->first()->id . '/edit');

    $this->assertDatabaseHas('categories', ['name' => 'Nueva Categoría']);
}

    #[Test]
    public function category_name_is_required()
    {
        $user = User::factory()->create()->assignRole('super_admin');
        $this->actingAs($user);

        Livewire::actingAs($user)
            ->test(\App\Filament\Resources\CategoryResource\Pages\CreateCategory::class)
            ->set('data.name', '')
            ->call('create')
            ->assertHasErrors(['data.name' => 'required']);

        $this->assertDatabaseMissing('categories', ['name' => '']);
    }

    #[Test]
    public function unauthorized_user_cannot_access_categories()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/categories');
        $response->assertForbidden();
    }

    #[Test]
    public function super_admin_can_update_a_category()
    {
        $user = User::factory()->create()->assignRole('super_admin');
        $this->actingAs($user);

        $category = Category::factory()->create(['name' => 'Categoría Original']);

        Livewire::actingAs($user)
            ->test(\App\Filament\Resources\CategoryResource\Pages\EditCategory::class, ['record' => $category->id])
            ->set('data.name', 'Categoría Actualizada')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', ['name' => 'Categoría Actualizada']);
    }

    #[Test]
    public function super_admin_can_delete_a_category_from_list()
    {
        $user = User::factory()->create()->assignRole('super_admin');
        $this->actingAs($user);

        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Filament\Resources\CategoryResource\Pages\ListCategories::class)
            ->callTableAction('delete', $category);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}