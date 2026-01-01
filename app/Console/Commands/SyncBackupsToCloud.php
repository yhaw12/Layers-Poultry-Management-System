<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class SyncBackupsToCloud extends Command
{
    protected $signature = 'backup:smart-sync';
    protected $description = 'Checks for internet, then syncs local monthly backups to cloud';

    public function handle()
    {
        $this->info('Checking internet connectivity...');

        if (!$this->isOnline()) {
            $this->warn('System is OFFLINE. Backup will remain local only.');
            return 0;
        }

        $this->info('Internet connected. Syncing backups...');

        // 1. Get Local Backups (assuming stored in storage/app/Laravel)
        // Adjust path based on your config/backup.php
        $path = storage_path('app/Laravel'); 
        $files = File::glob($path . '/*.zip');

        if (empty($files)) {
            $this->info('No local backups found to sync.');
            return 0;
        }

        // 2. Configure Cloud Disk (e.g., 'google', 's3')
        // Ensure you have configured this in config/filesystems.php
        $cloudDisk = Storage::disk('google'); // Change 'google' to your disk name (s3, dropbox, etc)

        foreach ($files as $file) {
            $filename = basename($file);

            // Check if file already exists on cloud
            if (!$cloudDisk->exists($filename)) {
                $this->info("Uploading: $filename");
                
                // Upload stream (better for large files)
                $stream = fopen($file, 'r+');
                $cloudDisk->writeStream($filename, $stream);
                fclose($stream);
                
                $this->info("Upload complete.");
            } else {
                $this->line("Skipping $filename (Already on cloud).");
            }
        }

        $this->info('Sync complete.');
        return 0;
    }

    /**
     * Simple check to see if we can reach Google
     */
    private function isOnline()
    {
        try {
            $response = Http::timeout(5)->get('https://www.google.com');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}