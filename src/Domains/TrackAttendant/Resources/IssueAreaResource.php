<?php

declare(strict_types=1);

namespace Domains\TrackAttendant\Resources;

use Domains\SuperAdmin\Resources\LineResource;
use Domains\SuperAdmin\Resources\SectionResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class IssueAreaResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'line' => new LineResource(
                resource: $this->whenLoaded(
                    relationship: 'line_id',
                ),
            ),
            'section' => $this->resource->section_id ? new SectionResource(
                resource: $this->whenLoaded(
                    relationship: 'section',
                ),
            ) : null,

            'station' => $this->resource->station_id ? new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'station',
                ),
            ) : null,

            'speed_suggestion' => $this->resource->speed_suggestion,
            'speed_suggestion_comment' => $this->resource->speed_suggestion_comment,
            'speed_suggestion_status' => $this->resource->speed_suggestion_status,
            'proposed_speed' => $this->resource->proposed_speed,
            'proposed_speed_comment' => $this->resource->proposed_speed_comment,

            'reverted_speed' => $this->resource->reverted_speed,
            'reverted_speed_comment' => $this->resource->reverted_speed_comment,
        ];
    }
}
