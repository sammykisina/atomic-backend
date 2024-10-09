<?php

declare(strict_types=1);

namespace Domains\Operator\Notifications;

use Domains\Driver\Models\License;
use Domains\Driver\Models\Path;
use Domains\Driver\Resources\PathResource;
use Domains\Shared\Enums\NotificationTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class LicenseNotification extends Notification
{
    use Queueable;

    public function __construct(
        public License $license,
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
        $paths = Path::query()->where('license_id', $this->license->id)->with(['originStation', 'fromMainLine', 'fromLoop', 'section', 'destinationStation', 'toMainLine', 'toLoop'])
            ->get();

        return [
            'license' => [
                'id' => $this->license->id,
                'journey_id' => $this->license->journey_id,
                'license_number' => $this->license->license_number,
                'path' => PathResource::collection(
                    resource: $paths,
                ),
            ],
            'type' => $this->type,
        ];

    }
}
