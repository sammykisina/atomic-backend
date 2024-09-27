<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Notifications;

use Domains\ReginalCivilEngineer\Models\InspectionSchedule;
use Domains\Shared\Enums\NotificationTypes;
use Domains\SuperAdmin\Resources\LineResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class InspectionScheduleNotification extends Notification
{
    use Queueable;

    public function __construct(
        public InspectionSchedule $inspectionSchedule,
        public NotificationTypes $type,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'inspection_schedule_id' => $this->inspectionSchedule->id,
            'line' => new LineResource(
                resource: $this->inspectionSchedule->line,
            ),
            'time' => $this->inspectionSchedule->time,
            'kilometer' => [
                'start' => $this->inspectionSchedule->start_kilometer,
                'end' => $this->inspectionSchedule->end_kilometer,
            ],
            'type' => $this->type,
        ];
    }
}
