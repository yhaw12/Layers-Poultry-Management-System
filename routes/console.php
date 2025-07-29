<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Existing schedules (e.g., birds:update-stages)
        // $schedule->command('birds:update-stages')->daily();
        // $schedule->command('inventory:check-low-stock')->daily();

        // // Add backup schedule
        // $schedule->command('backup:run')->daily()->at('02:00'); 

        // $schedule->command('sales:check-overdue')->daily();


       
    