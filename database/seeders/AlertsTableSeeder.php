<?php

// database/seeders/AlertsTableSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alert;
use Database\Factories\AlertFactory;

class AlertsTableSeeder extends Seeder
{
    public function run(): void
    {
        AlertFactory::new()->count(25)->create();
    }
}
