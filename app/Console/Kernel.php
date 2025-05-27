<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Existing schedules (e.g., birds:update-stages)
        $schedule->command('birds:update-stages')->daily();
        $schedule->command('inventory:check-low-stock')->daily();

        // Add backup schedule
        $schedule->command('backup:run')->daily()->at('02:00'); // Run at 2 AM
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}