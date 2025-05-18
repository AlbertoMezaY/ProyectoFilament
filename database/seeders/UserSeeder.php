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

        // SuperAdmin
        $super = User::create([
            'name' => 'Super Admin',
            'apellidos' =>"Admin",
            'edad' => 30,
            'email' => 'super@admin.com',
            'password' => Hash::make('password'), // cámbialo en producción
        ]);
        $super->assignRole('SuperAdmin');

        // Administrator
        $admin = User::create([
            'name' => 'Admin User',
            'apellidos' =>"Admin",
            'edad' => 30,
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Administrator');

        // Subscriber
        $subscriber = User::create([
            'name' => 'Subscriber User',
            'apellidos' =>"Admin",
            'edad' => 30,
            'email' => 'subscriber@example.com',
            'password' => Hash::make('password'),
        ]);
        $subscriber->assignRole('Subscriber');
    }
}
