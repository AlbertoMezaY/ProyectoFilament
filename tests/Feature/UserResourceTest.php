<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
       parent::setUp();

    $superAdmin = Role::findOrCreate('super_admin');
    $administrator = Role::findOrCreate('Administrator');
    $editor = Role::findOrCreate('Editor');
    $subscriber = Role::findOrCreate('Subscriber');

    $permissions = [
        'view_any_user',
        'view_user',
        'create_user',
        'update_user',
        'delete_user',
        'delete_any_user',
    ];
    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
    }
    $superAdmin->givePermissionTo($permissions);
    $administrator->givePermissionTo(['view_any_user', 'view_user', 'create_user', 'update_user']);
    $editor->givePermissionTo([]); // Editor sin permisos adicionales por ahora
    $subscriber->givePermissionTo([]); // Subscriber sin permisos adicionales
    }

    #[Test]
public function super_admin_can_create_a_user()
{
    $superAdmin = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($superAdmin);

    Livewire::actingAs($superAdmin)
        ->test(\App\Filament\Resources\UserResource\Pages\CreateUser::class)
        ->set('data.name', 'Nuevo Usuario')
        ->set('data.apellidos', 'Apellido Ejemplo')
        ->set('data.edad', '25')
        ->set('data.email', 'nuevo@ejemplo.com')
        ->set('data.password', 'password123')
        ->set('data.roles', [Role::where('name', 'Editor')->first()->id]) // Usa el ID del rol
        ->call('create')
        ->assertRedirect('/admin/users/' . User::latest()->first()->id . '/edit');

    $this->assertDatabaseHas('users', ['email' => 'nuevo@ejemplo.com']);
    $this->assertDatabaseHas('model_has_roles', ['model_id' => User::latest()->first()->id, 'role_id' => Role::where('name', 'Editor')->first()->id]);
}

    #[Test]
    public function user_name_is_required()
    {
        $superAdmin = User::factory()->create()->assignRole('super_admin');
        $this->actingAs($superAdmin);

        Livewire::actingAs($superAdmin)
            ->test(\App\Filament\Resources\UserResource\Pages\CreateUser::class)
            ->set('data.name', '')
            ->set('data.apellidos', 'Apellido Ejemplo')
            ->set('data.edad', '25')
            ->set('data.email', 'nuevo@ejemplo.com')
            ->set('data.password', 'password123')
            ->call('create')
            ->assertHasErrors(['data.name' => 'required']);

        $this->assertDatabaseMissing('users', ['name' => '']);
    }

    #[Test]
    public function unauthorized_user_cannot_access_users()
    {
        $user = User::factory()->create()->assignRole('Subscriber'); // Rol sin permisos
        $this->actingAs($user);

        $response = $this->get('/admin/users');
        $response->assertForbidden();
    }

    #[Test]
public function super_admin_can_update_a_user()
{
    $superAdmin = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($superAdmin);

    $user = User::factory()->create([
        'name' => 'Usuario Original',
        'apellidos' => 'Apellido Original',
        'edad' => '30',
        'email' => 'original@ejemplo.com',
    ])->assignRole('Editor');

    Livewire::actingAs($superAdmin)
        ->test(\App\Filament\Resources\UserResource\Pages\EditUser::class, ['record' => $user->id])
        ->set('data.name', 'Usuario Actualizado')
        ->set('data.apellidos', 'Apellido Actualizado')
        ->set('data.edad', '35')
        ->set('data.email', 'actualizado@ejemplo.com')
        ->set('data.roles', [Role::where('name', 'Administrator')->first()->id]) // Usa el ID del rol
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', ['email' => 'actualizado@ejemplo.com']);
    $this->assertDatabaseHas('model_has_roles', ['model_id' => $user->id, 'role_id' => Role::where('name', 'Administrator')->first()->id]);
}


    #[Test]
    public function super_admin_can_delete_a_user_from_list()
    {
        $superAdmin = User::factory()->create()->assignRole('super_admin');
        $userToDelete = User::factory()->create()->assignRole('Editor');
        $this->actingAs($superAdmin);

        Livewire::actingAs($superAdmin)
            ->test(\App\Filament\Resources\UserResource\Pages\ListUsers::class)
            ->callTableAction('delete', $userToDelete);

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    #[Test]
public function super_admin_cannot_delete_another_super_admin()
{
    $superAdmin1 = User::factory()->create()->assignRole('super_admin');
    $superAdmin2 = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($superAdmin1);

    Livewire::actingAs($superAdmin1)
        ->test(\App\Filament\Resources\UserResource\Pages\ListUsers::class)
        ->assertTableActionHidden('delete', $superAdmin2); // Verifica que la acción esté oculta

    $this->assertDatabaseHas('users', ['id' => $superAdmin2->id]);
}

 /*#[Test]
public function super_admin_cannot_update_another_super_admin()
{
    $superAdmin1 = User::factory()->create()->assignRole('super_admin');
    $superAdmin2 = User::factory()->create()->assignRole('super_admin');
    $this->actingAs($superAdmin1);

    Livewire::actingAs($superAdmin1)
        ->test(\App\Filament\Resources\UserResource\Pages\EditUser::class, ['record' => $superAdmin2->id])
        ->assertSee('<input disabled ', false) // Verifica la presencia del atributo disabled en cualquier input
        ->assertSee('id="data.name"', false)   // Verifica específicamente el ID del campo name
        ->assertSee('id="data.apellidos"', false) // Verifica el ID del campo apellidos
        ->assertSee('id="data.edad"', false)   // Verifica el ID del campo edad
        ->assertSee('id="data.email"', false)  // Verifica el ID del campo email
        ->assertSee($superAdmin2->name, false) // Asegura que el nombre original esté presente
        ->assertSee($superAdmin2->apellidos, false); // Asegura que el apellido original esté presente

    $this->assertDatabaseHas('users', ['id' => $superAdmin2->id, 'name' => $superAdmin2->name]);
}*/

}