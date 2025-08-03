<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pen;
use App\Models\User;
use App\Models\Egg;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Pen::create(['name' => 'Flock A']);
        Pen::create(['name' => 'Flock B']);
        User::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => bcrypt('password')]);
        Egg::factory()->count(10)->create();
    }
}