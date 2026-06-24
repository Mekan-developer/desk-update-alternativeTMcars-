<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'phone'    => '+99312345678',
            'email'    => 'admin@gmail.com',
            'name'     => 'Администратор',
            'role'     => 'admin',
            'status'   => 'active',
            'password' => Hash::make('password'),
        ]);
        User::create([
            'phone'    => '+99361234567',
            'email'    => 'manager@gmail.com',
            'name'     => 'Менеджер',
            'role'     => 'manager',
            'status'   => 'active',
            'password' => Hash::make('password'),
        ]);
    }
}
