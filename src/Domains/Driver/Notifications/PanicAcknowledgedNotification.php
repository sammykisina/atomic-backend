<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Driver\Models\Panic;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class PanicAcknowledgedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Panic $panic,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**  @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => NotificationTypes::PANIC_ACKNOWLEDGED->value,
            'message' => 'Panic Acknowledged',
            'description' => 'A panic you send with train ' . $this->panic->journey->train->name . ' was acknowledged.',
        ];
    }
}
