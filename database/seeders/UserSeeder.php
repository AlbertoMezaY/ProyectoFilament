<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
       /* User::create([
            'name' => 'Administrador',
            'apellidos' =>"Admin",
            'edad' => 30,
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin@admin.com'), // Cambia esto por una contraseña segura si gustas
        ]);*/

        


        // Administrator
        $admin = User::create([
            'name' => 'Super Administrador',
            'apellidos' =>"Admin",
            'edad' => 20,
            'email' => 'superadmin@example.com',
            'password' => Hash::make('superadmin@example.com'),
        ]);
        $admin->assignRole('super_admin');

        
    }

}
