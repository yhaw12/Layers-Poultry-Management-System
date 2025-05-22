<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Admin user
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'), // You can change this
        'is_admin' => true,
    ]);

    // Regular user
    User::create([
        'name' => 'Regular User',
        'email' => 'user@example.com',
        'password' => Hash::make('password'), // You can change this
        'is_admin' => false,
    ]);
    }
}
