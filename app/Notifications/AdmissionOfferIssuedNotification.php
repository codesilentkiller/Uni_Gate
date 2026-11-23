<?php

namespace App\Notifications;

use App\Models\AdmissionOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdmissionOfferIssuedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public AdmissionOffer $offer)
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
            ->subject('Admission Offer — '.$this->offer->offer_reference)
            ->line('A formal admission offer has been issued to you.')
            ->line('Offer reference: '.$this->offer->offer_reference)
            ->action('View Offer', url('/admission-offers'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'offer_id' => $this->offer->id,
            'offer_reference' => $this->offer->offer_reference,
            'application_id' => $this->offer->application_id,
        ];
    }
}
