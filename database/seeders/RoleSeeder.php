<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
         // Roles
        $roles = [
            'Super Admin',
            'Administrator',
            'Editor',
            'Author',
            'Contributor',
            'Subscriber',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Permisos
        $permissions = Permission::pluck('name')->toArray();

        // Asignar todos los permisos al Super Admin
        Role::findByName('super admin')->syncPermissions($permissions);

        // Permisos para Administrator
        Role::findByName('administrator')->syncPermissions([
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            'restore_user',
            'restore_any_user',
            'replicate_user',
            'reorder_user',
            'force_delete_user',
            'force_delete_any_user',
        ]);

        // Permisos para Editor
        Role::findByName('editor')->syncPermissions([
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
        ]);

        // Permisos para Author
        Role::findByName('author')->syncPermissions([
            'view_user',
            'view_any_user',
        ]);

        // Permisos para Contributor
        Role::findByName('contributor')->syncPermissions([]);

        // Permisos para Subscriber
        Role::findByName('subscriber')->syncPermissions([]);
    }
}