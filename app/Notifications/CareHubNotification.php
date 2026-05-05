<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CareHubNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $type,
        private readonly string $summary,
        private readonly array  $data = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return array_merge($this->data, [
            'type'    => $this->type,
            'summary' => $this->summary,
        ]);
    }
}
