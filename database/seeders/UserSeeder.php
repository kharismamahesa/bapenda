<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TUser;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TUser::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator Bapenda',
                'email' => 'admin@bapenda.go.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}

