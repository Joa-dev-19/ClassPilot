<?php

namespace App\Notifications;

use App\Models\Tache;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RappelTacheNotification extends Notification
{
    use Queueable;

    public function __construct(public Tache $tache) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rappel : échéance de votre tâche')
            ->markdown('emails.rappels.tache', [
                'recipientName' => $notifiable->name,
                'taskTitle' => $this->tache->titre,
                'deadline' => Carbon::parse($this->tache->date_limite)->format('d/m/Y'),
                'tasksUrl' => route('taches.index'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tache_id' => $this->tache->id,
            'titre' => $this->tache->titre,
            'date_limite' => Carbon::parse($this->tache->date_limite)->toDateString(),
        ];
    }
}
