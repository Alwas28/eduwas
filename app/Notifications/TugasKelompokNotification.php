<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TugasKelompokNotification extends Notification
{
    public function __construct(
        private string $title,
        private string $body,
        private string $url,
        private string $icon  = 'fa-users',
        private string $color = 'blue',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'tugas',
            'icon'  => $this->icon,
            'color' => $this->color,
            'title' => $this->title,
            'body'  => $this->body,
            'url'   => $this->url,
        ];
    }
}
