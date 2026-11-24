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
        $application = $this->decision->application->loadMissing(['programme.department.faculty', 'institution', 'admissionCycle']);
        $tuitionFee = $application->programme->tuition_fee
            ?: $application->programme->department->faculty->tuition_fee
            ?: 0;

        $mail = (new MailMessage)
            ->subject('Admission Approved — '.$application->application_reference)
            ->greeting('Congratulations, '.$notifiable->first_name.'!')
            ->line('Your application '.$application->application_reference.' has been approved for '.$application->programme->name.'.')
            ->line('Institution: '.$application->institution->name)
            ->line('Programme tuition fee: Le '.number_format((float) $tuitionFee, 2));

        if ($this->decision->decision === 'approved') {
            $mail->line('Please pay the required fees and complete registration before the institution’s deadline.')
                ->action('Pay Fees', route('applicant.payments'));
        } else {
            $mail->line('Decision: '.ucwords($this->decision->decision));
        }

        return $mail->action('View Application', route('applicant.applications.show', $application));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $application = $this->decision->application->loadMissing(['programme.department.faculty']);
        $tuitionFee = $application->programme->tuition_fee ?: $application->programme->department->faculty->tuition_fee ?: 0;

        return [
            'title' => $this->decision->decision === 'approved' ? 'Admission approved — fee payment required' : 'Admission decision received',
            'message' => $this->decision->decision === 'approved'
                ? 'Your application was approved. Programme tuition fee: Le '.number_format((float) $tuitionFee, 2).'. Please pay the required fees and complete registration.'
                : 'Decision: '.ucwords($this->decision->decision).'.',
            'decision_id' => $this->decision->id,
            'application_id' => $this->decision->application_id,
            'decision' => $this->decision->decision,
            'tuition_fee' => (float) $tuitionFee,
            'action' => 'payments',
        ];
    }
}
