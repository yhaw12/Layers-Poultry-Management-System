<?php

namespace App\Console\Commands;

use App\Models\Bird;
use App\Notifications\BirdStageTransitionNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class UpdateBirdStages extends Command
{
    protected $signature = 'birds:update-stages';
    protected $description = 'Update bird stages based on their age';

    public function handle()
    {
        $birds = Bird::whereIn('stage', ['chick', 'juvenile'])->get();
        $admins = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($birds as $bird) {
            // Calculate age based on entry_date or purchase_date
            $referenceDate = $bird->purchase_date ?? $bird->entry_date;
            $ageInWeeks = Carbon::parse($referenceDate)->diffInWeeks(Carbon::now());

            // Update age field
            $bird->age = $ageInWeeks;
            $bird->save();

            // Transition logic
            if ($bird->stage === 'chick' && $ageInWeeks >= 4) {
                $bird->stage = 'juvenile';
                $bird->save();
                Notification::send($admins, new BirdStageTransitionNotification($bird, 'juvenile'));
                $this->info("Updated bird ID {$bird->id} to juvenile stage.");
            } elseif ($bird->stage === 'juvenile') {
                $maturityAge = $bird->type === 'layer' ? 16 : 6;
                if ($ageInWeeks >= $maturityAge) {
                    $bird->stage = 'adult';
                    $bird->save();
                    Notification::send($admins, new BirdStageTransitionNotification($bird, 'adult'));
                    $this->info("Updated bird ID {$bird->id} to adult stage.");
                }
            }
        }

        $this->info('Bird stages updated successfully.');
    }
}