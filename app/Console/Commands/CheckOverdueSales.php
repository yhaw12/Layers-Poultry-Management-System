<?php

namespace App\Console\Commands;

use App\Models\Sale;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckOverdueSales extends Command
{
    protected $signature = 'sales:check-overdue';
    protected $description = 'Update status of overdue sales';

    public function handle()
    {
        Sale::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now())
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->update(['status' => 'overdue']);

        $this->info('Overdue sales updated.');
    }
}