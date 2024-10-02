<?php

declare(strict_types=1);

namespace Domains\Inspector\Resources;

use Domains\Inspector\Models\Inspection;
use Domains\PermanentWayInspector\Resources\InspectionScheduleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Inspection $resource */
final class InspectionResource extends JsonResource
{
    /**  @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'date' => $this->resource->date,
            'start_time' => $this->resource->start_time,
            'end_time' => $this->resource->end_time,
            'is_active' => $this->resource->is_active,
            'issues' => IssueResource::collection(
                resource: $this->whenLoaded(
                    relationship: 'issues',
                ),
            ),
            // 'issues_count' => $this->resource->issues_count,
            'inspection_schedule' => new InspectionScheduleResource(
                resource: $this->whenLoaded(
                    relationship: 'inspectionSchedule',
                ),
            ),
        ];
    }
}
