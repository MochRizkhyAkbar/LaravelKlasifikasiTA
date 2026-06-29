<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

         // 1. Membuat List Role
        $roles = [
            'admin',
            'bidangSDA',
            'bidangBINKON',
            'bidangTATARUANG',
            'bidangJALAN'
        ];

        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName);
        }

        // 2. Membuat User dan Assign Role Otomatis
        $users = [
            [
                'name'     => 'Admin Utama',
                'email'    => 'admin@example.com',
                'password' => 'password123',
                'role'     => 'admin',
            ],
            [
                'name'     => 'User Bidang SDA',
                'email'    => 'sda@example.com',
                'password' => 'password123',
                'role'     => 'bidangSDA',
            ],
            [
                'name'     => 'User Bidang BINKON',
                'email'    => 'binkon@example.com',
                'password' => 'password123',
                'role'     => 'bidangBINKON',
            ],
            [
                'name'     => 'User Bidang Tata Ruang',
                'email'    => 'tataruang@example.com',
                'password' => 'password123',
                'role'     => 'bidangTATARUANG',
            ],
            [
                'name'     => 'User Bidang Jalan',
                'email'    => 'jalan@example.com',
                'password' => 'password123',
                'role'     => 'bidangJALAN',
            ],
        ];

        foreach ($users as $userData) {
            // Buat user baru atau update jika email sudah ada
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            // Pasangkan role ke user
            $user->assignRole($userData['role']);
        }
    }
}
