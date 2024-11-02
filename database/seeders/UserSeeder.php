<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'username' => 'superadmin',
            'password' => Hash::make('password'), // Pastikan untuk mengganti password ini dalam produksi
            'role' => 'superadmin',
            'foto' => '',
        ]);

        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'username' => 'adminuser',
            'password' => Hash::make('password'), // Ganti password ini juga
            'role' => 'admin',
            'foto' => '',
        ]);

        // Dishub
        User::create([
            'name' => 'Dishub User',
            'email' => 'dishub@example.com',
            'username' => 'dishubuser',
            'password' => Hash::make('password'), // Ganti password ini juga
            'role' => 'dishub',
            'foto' => '',
        ]);
    }
}