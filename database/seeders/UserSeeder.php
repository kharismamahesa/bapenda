<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bapenda.go.id'],
            [
                'name' => 'Administrator Bapenda',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'operator@bapenda.go.id'],
            [
                'name' => 'Operator Konten & Layanan',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );
    }
}
