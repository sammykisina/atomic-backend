<?php

declare(strict_types=1);

namespace Domains\Operator\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class DeclineLineEntryRequestNotification extends Notification
{
    use Queueable;

    /**
     * CREATE A NEW NOTIFICATION INSTANCE
     */
    public function __construct(
        public Journey $journey,
        public string $reason_for_decline,
    ) {}

    /**
     * GET THE NOTIFICATION'S DELIVERY CHANNELS
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * GET THE MAIL REPRESENTATION OF THE NOTIFICATION
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Line entry request declined.',
            'description' => 'Your line entry request for train ' . $this->journey->train->trainName->name . " was declined with a reason [ " . $this->reason_for_decline . " ]",
            'type' => NotificationTypes::LINE_ENTRY_REQUEST_DECLINED->value,
        ];
    }
}
