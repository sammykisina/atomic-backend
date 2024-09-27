<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Resources;

use Domains\ReginalCivilEngineer\Models\InspectionSchedule;
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
            'start_kilometer' => $this->resource->start_kilometer,
            'end_kilometer' => $this->resource->end_kilometer,
        ];
    }
}
