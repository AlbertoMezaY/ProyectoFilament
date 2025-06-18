<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class TagResourceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $superAdmin = Role::findOrCreate('super_admin');
        $permissions = [
            'view_any_tag',
            'view_tag',
            'create_tag',
            'update_tag',
            'delete_tag',
        ];
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }
        $superAdmin->givePermissionTo($permissions);
    }

    #[Test]
public function super_admin_can_create_a_tag()
{
    $user = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($user);

    Livewire::actingAs($user)
        ->test(\App\Filament\Resources\TagResource\Pages\CreateTag::class)
        ->set('data.name', 'Nueva Etiqueta')
        ->call('create')
        ->assertRedirect('/admin/tags/' . Tag::latest()->first()->id . '/edit');

    $this->assertDatabaseHas('tags', ['name' => 'Nueva Etiqueta']);
}

    #[Test]
    public function tag_name_is_required()
    {
        $user = User::factory()->create()->assignRole('super_admin');
        $this->actingAs($user);

        Livewire::actingAs($user)
            ->test(\App\Filament\Resources\TagResource\Pages\CreateTag::class)
            ->set('data.name', '')
            ->call('create')
            ->assertHasErrors(['data.name' => 'required']);

        $this->assertDatabaseMissing('tags', ['name' => '']);
    }

    #[Test]
    public function unauthorized_user_cannot_access_tags()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/admin/tags');
        $response->assertForbidden();
    }

    #[Test]
    public function super_admin_can_update_a_tag()
    {
        $user = User::factory()->create()->assignRole('super_admin');
        $this->actingAs($user);

        $tag = Tag::factory()->create(['name' => 'Etiqueta Original']);

        Livewire::actingAs($user)
            ->test(\App\Filament\Resources\TagResource\Pages\EditTag::class, ['record' => $tag->id])
            ->set('data.name', 'Etiqueta Actualizada')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tags', ['name' => 'Etiqueta Actualizada']);
    }

    #[Test]
    public function super_admin_can_delete_a_tag_from_list()
    {
        $user = User::factory()->create()->assignRole('super_admin');
        $this->actingAs($user);

        $tag = Tag::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Filament\Resources\TagResource\Pages\ListTags::class)
            ->callTableAction('delete', $tag);

        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}