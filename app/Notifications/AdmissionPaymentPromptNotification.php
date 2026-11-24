<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdmissionPaymentPromptNotification extends Notification
{
    use Queueable;

    public function __construct(public Application $application, public float $tuitionFee) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Admission Approved — Pay Your Fees')
            ->greeting('Congratulations, '.$notifiable->first_name.'!')
            ->line('Your application '.$this->application->application_reference.' has been approved.')
            ->line('Programme: '.$this->application->programme->name)
            ->line('Programme tuition fee: Le '.number_format($this->tuitionFee, 2))
            ->line('Please complete payment through the officially approved university payment channels and keep your proof of payment.')
            ->action('Open Payments', route('applicant.payments'))
            ->line('Payment instructions may change if the university publishes updated programme fees.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Admission approved — fee payment required',
            'message' => 'Tuition fee: Le '.number_format($this->tuitionFee, 2).'. Complete payment to continue registration.',
            'application_id' => $this->application->id,
            'tuition_fee' => $this->tuitionFee,
            'action' => 'payments',
        ];
    }
}
