<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the poultry database offline (Windows/XAMPP ready, raw SQL)';

    public function handle()
    {
        $backupPath = storage_path('app/backups');

        // Create folder if it doesn't exist
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0777, true);
        }

        $date = date('Y-m-d_H-i-s');
        $file = $backupPath . "/poultry_backup_{$date}.sql";

        $db = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');

        // XAMPP MySQL path
        $mysqldump = '"C:\xampp\mysql\bin\mysqldump.exe"';

        // Build command (no password)
        $command = "$mysqldump -h $host -u $user $db > \"$file\"";

        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Backup saved: $file");
        } else {
            $this->error("Backup failed. Check mysqldump path, DB credentials, and folder permissions.");
        }

        // Auto-delete backups older than 30 days
        foreach (File::files($backupPath) as $old) {
            if ($old->getMTime() < now()->subDays(30)->timestamp) {
                File::delete($old->getPathname());
            }
        }
    }
}
