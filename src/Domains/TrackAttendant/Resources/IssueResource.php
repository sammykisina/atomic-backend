<?php

declare(strict_types=1);

namespace Domains\TrackAttendant\Resources;

use Domains\SeniorTrackInspector\Resources\AssignmentResource;
use Domains\Shared\Resources\DateResource;
use Domains\TrackAttendant\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Issue $resource */
final class IssueResource extends JsonResource
{
    /** @return array<string, mixed>*/
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'issue_name' => new IssueNameResource(
                resource: $this->whenLoaded(
                    relationship: 'issueName',
                ),
            ),
            'description' => $this->resource->description,
            'condition' => $this->resource->condition,
            'latitude' => $this->resource->latitude,
            'longitude' => $this->resource->longitude,
            'image_url' => $this->resource->image_url,
            'created_at' => new DateResource(
                resource: $this->resource->created_at,
            ),
            'inspection' => new InspectionResource(
                resource: $this->whenLoaded(
                    relationship: 'inspection',
                ),
            ),
            'status' => $this->resource->status,
            'assignment' => new AssignmentResource(
                resource: $this->whenLoaded(
                    relationship: 'assignment',
                ),
            ),
            'issue_kilometer' => $this->resource->issue_kilometer,
            'issue_area' => new IssueAreaResource(
                $this->whenLoaded(
                    relationship: 'issueArea',
                ),
            ),
        ];
    }
}
