<?php

return [

    'backup' => [
        /*
         * The name of this application. You can use this name to monitor
         * the backups.
         */
        'name' => env('APP_NAME', 'PoultryTracker'),

        'source' => [
            'files' => [
                /*
                 * The list of directories and files that will be included in the backup.
                 */
                'include' => [
                    storage_path('app'),
                ],

                /*
                 * These directories and files will be excluded from the backup.
                 */
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    storage_path('logs'),
                ],

                /*
                 * Determines if symlinks should be followed.
                 */
                'follow_links' => false,

                /*
                 * Determines if it should avoid unreadable folders.
                 */
                'ignore_unreadable_directories' => false,

                /*
                 * This path is used to make directories in resulting zip-file relative
                 * Set to `null` to include full paths
                 */
                'relative_path' => null,
            ],

            /*
             * The names of the connections to the databases that should be backed up
             */
            'databases' => [
                'mysql', // Adjust based on your database (e.g., 'sqlite' if using SQLite)
            ],
        ],

        /*
         * The database dump can be compressed to decrease disk space usage.
         */
        'database_dump_compressor' => null,

        /*
         * The file extension used for the database dump files.
         */
        'database_dump_file_extension' => '',

        'destination' => [
            /*
             * The filename prefix used for the backup zip file.
             */
            'filename_prefix' => 'poultry_tracker_backup_',

            /*
             * The disk names where backups should be stored.
             */
            'disks' => [
                'local', // Store backups locally
            ],
        ],

        /*
         * The directory where the temporary files will be stored.
         */
        'temporary_directory' => storage_path('app/backup-temp'),

        /*
         * The password to be used for archive encryption.
         * Set to `null` to disable encryption.
         */
        'password' => null,

        /*
         * The encryption algorithm to be used for archive encryption.
         * You can set it to `null` or `false` to disable encryption.
         */
        'encryption' => 'default',
    ],

    /*
     * You can get notified when specific events occur.
     */
    'notifications' => [
        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => [],
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent.
         */
        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
             'to' => env('BACKUP_MAIL_TO')  // Disable email notifications for offline use
        ],

        // Disable other notification channels
    //    'slack' => [
    // 'webhook_url' => '',
    // 'channel' => '',
    // 'username' => '',
    // 'icon' => '',
    // ],


    ],

    /*
     * Here you can specify which backups should be monitored.
     */
    'monitor_backups' => [
        [
            'name' => env('APP_NAME', 'PoultryTracker'),
            'disks' => ['local'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        /*
         * The strategy that will be used to cleanup old backups.
         */
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [
            /*
             * The number of days for which backups must be kept.
             */
            'keep_all_backups_for_days' => 7,

            /*
             * The number of days for which daily backups must be kept.
             */
            'keep_daily_backups_for_days' => 16,

            /*
             * The number of weeks for which one weekly backup must be kept.
             */
            'keep_weekly_backups_for_weeks' => 8,

            /*
             * The number of months for which one monthly backup must be kept.
             */
            'keep_monthly_backups_for_months' => 4,

            /*
             * The number of years for which one yearly backup must be kept.
             */
            'keep_yearly_backups_for_years' => 2,

            /*
             * After cleaning up the backups remove the oldest backup until
             * this amount of megabytes has been reached.
             */
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],
];