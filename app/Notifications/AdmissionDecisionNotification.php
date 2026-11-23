<?php

namespace App\Notifications;

use App\Models\AdmissionDecision;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdmissionDecisionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public AdmissionDecision $decision)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('UniGate Admission Decision')
            ->line('An admission decision has been recorded for application ' . $this->decision->application->application_reference . '.')
            ->line('Decision: ' . ucwords($this->decision->decision))
            ->action('View Application', url('/applications/' . $this->decision->application_id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'decision_id' => $this->decision->id,
            'application_id' => $this->decision->application_id,
            'decision' => $this->decision->decision,
        ];
    }
}
