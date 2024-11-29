<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Notifications;

use Domains\SuperAdmin\Enums\ShiftNotificationTypes;
use Domains\SuperAdmin\Models\Shift;
use Domains\SuperAdmin\Resources\ShiftManagement\ShiftResource;
use Domains\SuperAdmin\Services\ShiftManagement\ShiftService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class ShiftNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Shift $shift,
        public ShiftNotificationTypes $type,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**  @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $shift = ShiftService::getShiftById(
            shift_id: $this->shift->id,
        );

        return [
            'shift' => new ShiftResource(
                resource: $shift,
            ),
            'type' => $this->type,
        ];
    }
}
