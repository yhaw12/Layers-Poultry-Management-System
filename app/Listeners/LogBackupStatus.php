<?php

namespace App\Listeners;

use App\Models\Alert;
use Spatie\Backup\Events\BackupWasSuccessful;
use Spatie\Backup\Events\BackupHasFailed;

class LogBackupStatus
{
    public function handle($event)
    {
        if ($event instanceof BackupWasSuccessful) {
            Alert::create([
                'message' => 'Backup completed successfully on ' . now()->toDateTimeString(),
                'type' => 'backup_success',
            ]);
        } elseif ($event instanceof BackupHasFailed) {
            Alert::create([
                'message' => 'Backup failed: ' . $event->exception->getMessage(),
                'type' => 'backup_failed',
            ]);
        }
    }
}