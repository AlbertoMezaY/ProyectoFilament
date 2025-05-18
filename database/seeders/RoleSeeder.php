<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear permisos si no existen
        $permissions = [
            'view users',
            'create users',
            'update users',
            'delete users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Crear roles
        $super = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $admin = Role::firstOrCreate(['name' => 'Administrator']);
        $subscriber = Role::firstOrCreate(['name' => 'Subscriber']);

        // 3. Asignar permisos
        $super->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'view users',
            'create users',
            'update users',
        ]);

        $subscriber->syncPermissions([
            'view users',
        ]);
    }
}