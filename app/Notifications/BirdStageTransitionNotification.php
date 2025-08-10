<?php

namespace App\Notifications;

use App\Models\Bird;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BirdStageTransitionNotification extends Notification
{
    use Queueable;

    protected $bird;
    protected $newStage;

    public function __construct(Bird $bird, string $newStage)
    {
        $this->bird = $bird;
        $this->newStage = $newStage;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Bird Batch Stage Transition')
            ->line("Bird batch ID {$this->bird->id} ({$this->bird->breed}, {$this->bird->type}) has transitioned to {$this->newStage} stage.")
            ->action('View Bird', route('birds.edit', $this->bird->id))
            ->line('Thank you for using the Poultry Tracker!');
    }

    public function toArray($notifiable)
    {
        return [
            'bird_id' => $this->bird->id,
            'breed' => $this->bird->breed,
            'type' => $this->bird->type,
            'new_stage' => $this->newStage,
            'message' => "Bird batch ID {$this->bird->id} transitioned to {$this->newStage} stage.",
            'url' => route('birds.edit', $this->bird->id),
            'type' => 'info'
        ];
    }
}