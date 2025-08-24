<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Define scheduled tasks
Schedule::command('birds:update-stages')->daily();

// Schedule::command('inventory:check-low-stock')->daily();
// Schedule::command('backup:run')->daily()->at('02:00');
// Schedule::command('sales:check-overdue')->daily();