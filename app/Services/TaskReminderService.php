<?php

namespace App\Services;

use App\Models\Egg;
use App\Models\Reminder;
use App\Models\EggLog;
use App\Models\Payroll;
use App\Models\MaintenanceLog;
use App\Models\VaccinationLog;
use Carbon\Carbon;

class TaskReminderService
{
    public function checkAll(): void
    {
        $this->eggLogs();
        $this->payroll();
        $this->sawdustDue();
        $this->vaccinations();
    }

    private function upsert(array $data): void
    {
        Reminder::updateOrCreate(
            ['type' => $data['type'], 'due_date' => $data['due_date'] ?? null],
            $data + ['is_done' => false]
        );
    }

    public function eggLogs(): void
    {
        $days = (int) config('reminders.egg_log_days', 2);
        $last = Egg::latest('date')->first();
        if (! $last || Carbon::parse($last->date)->lt(Carbon::today()->subDays($days))) {
            $this->upsert([
                'type' => 'egg_log_missing',
                'title' => 'Egg logs missing',
                'message' => 'No egg record in the last ' . $days . ' days',
                'due_date' => Carbon::today()->toDateString(),
                'severity' => 'warning',
            ]);
        } else {
            Reminder::where('type','egg_log_missing')->update(['is_done' => true]);
        }
    }

    public function payroll(): void
    {
        $last = Payroll::latest('created_at')->first();
        $due = Carbon::now()->endOfMonth();
        $hasPayroll = $last && Carbon::parse($last->created_at)->isSameMonth(Carbon::now());

        if (! $hasPayroll && Carbon::today()->greaterThanOrEqualTo($due)) {
            $this->upsert([
                'type' => 'payroll_overdue',
                'title' => 'Payroll overdue',
                'message' => 'No payroll recorded for ' . Carbon::now()->format('F Y'),
                'due_date' => $due->toDateString(),
                'severity' => 'critical',
            ]);
        } else {
            Reminder::where('type','payroll_overdue')->update(['is_done' => true]);
        }
    }

    public function sawdustDue(): void
    {
        $cycle = (int) config('reminders.sawdust_cycle_days');
        $last = MaintenanceLog::where('task','sawdust_change')->latest('performed_at')->first();
        $due = $last ? Carbon::parse($last->performed_at)->addDays($cycle) : Carbon::today();

        if (Carbon::today()->greaterThanOrEqualTo($due)) {
            $this->upsert([
                'type' => 'sawdust_due',
                'title' => 'Sawdust change due',
                'message' => $last
                    ? 'Last change: '.Carbon::parse($last->performed_at)->toFormattedDateString()
                    : 'No record of sawdust change found.',
                'due_date' => $due->toDateString(),
                'severity' => 'warning',
            ]);
        } else {
            Reminder::where('type','sawdust_due')->update(['is_done' => true]);
        }
    }

    public function vaccinations(): void
    {
        $warnDays = (int) config('reminders.vaccination_warn_days');
        $warnDate = Carbon::today()->addDays($warnDays);

        // Overdue
        $overdue = VaccinationLog::whereDate('next_vaccination_date','<',Carbon::today())->get();
        foreach ($overdue as $log) {
            $this->upsert([
                'type' => 'vaccination_overdue',
                'title' => 'Vaccination overdue',
                'message' => 'Next dose was due '.Carbon::parse($log->next_vaccination_date)->toFormattedDateString(),
                'due_date' => $log->next_vaccination_date,
                'severity' => 'critical',
                'meta' => ['id' => $log->id],
            ]);
        }

        // Upcoming
        $upcoming = VaccinationLog::whereBetween('next_vaccination_date',[Carbon::today(), $warnDate])->get();
        foreach ($upcoming as $log) {
            $this->upsert([
                'type' => 'vaccination_upcoming',
                'title' => 'Vaccination upcoming',
                'message' => 'Due on '.Carbon::parse($log->next_vaccination_date)->toFormattedDateString(),
                'due_date' => $log->next_vaccination_date,
                'severity' => 'info',
                'meta' => ['id' => $log->id],
            ]);
        }

        Reminder::where('type','vaccination_upcoming')
            ->whereDate('due_date','<',Carbon::today())
            ->update(['is_done'=>true]);
    }
}
