<?php

declare(strict_types=1);

namespace Domains\SeniorTrackInspector\Resources;

use Domains\SeniorTrackInspector\Models\InspectionSchedule;
use Domains\Shared\Resources\UserResource;
use Domains\SuperAdmin\Resources\LineResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read InspectionSchedule $resource */
final class InspectionScheduleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'time' => $this->resource->time,
            'status' => $this->resource->status,
            'line' => new LineResource(
                resource: $this->whenLoaded(
                    relationship: 'line',
                ),
            ),
            'inspector' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'inspector',
                ),
            ),
            'owner' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'owner',
                ),
            ),
            'start_kilometer' => $this->resource->start_kilometer,
            'end_kilometer' => $this->resource->end_kilometer,
            'start_kilometer_latitude' => $this->resource->start_kilometer_latitude,
            'start_kilometer_longitude' => $this->resource->start_kilometer_longitude,

            'end_kilometer_latitude' => $this->resource->end_kilometer_latitude,
            'end_kilometer_longitude' => $this->resource->end_kilometer_longitude,
        ];
    }
}
