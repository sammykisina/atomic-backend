<?php

declare(strict_types=1);

namespace Domains\Driver\Notifications;

use Domains\Driver\Models\Journey;
use Domains\Driver\Models\License;
use Domains\Shared\Enums\NotificationTypes;
use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Resources\LoopResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class LicenseRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Journey $journey,
        public License $lastLicense,
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
            'journey_id' => $this->journey->id,
            'journey_origin' => new StationResource(
                resource: $this->journey->origin,
            ),
            'journey_destination' => new StationResource(
                resource: $this->journey->destination,
            ),
            'driver' => new UserResource(
                resource: $this->journey->driver,
            ),
            'last_license_origin_station' =>  new StationResource(
                resource: $this->lastLicense->originStation,
            ),
            'last_license_destination_station' =>  new StationResource(
                resource: $this->lastLicense->destinationStation,
            ),
            'current_location_on_the_destination_station' => $this->lastLicense->main ? 'on the main line' : new LoopResource($this->lastLicense->loop),
            'type' => $this->type,
        ];
    }
}
