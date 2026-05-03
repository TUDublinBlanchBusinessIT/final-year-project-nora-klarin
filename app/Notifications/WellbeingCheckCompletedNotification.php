<?php

namespace App\Notifications;

use App\Models\WellbeingCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WellbeingCheckCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly WellbeingCheck $check,
        private readonly array $summary,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'log'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Wellbeing check completed')
            ->line('A new wellbeing check has been completed for ' . $this->check->youngPerson?->name)
            ->line('Overall wellbeing score: ' . round($this->summary['overall_wb_score'], 2))
            ->line('Risk classification: ' . ucfirst($this->summary['risk_classification']))
            ->action('View case', url(route('socialworker.cases.show', $this->check->case_file_id)))
            ->line('Please review the case and check any generated alerts.');
    }

    public function toLog(object $notifiable): string
    {
        return sprintf(
            'Wellbeing check completed for %s (case %s): score=%s risk=%s',
            $this->check->youngPerson?->name,
            $this->check->case_file_id,
            $this->summary['overall_wb_score'],
            $this->summary['risk_classification'],
        );
    }
}
