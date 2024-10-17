<?php

declare(strict_types=1);

namespace Domains\SeniorTrackInspector\Resources;

use Domains\SeniorTrackInspector\Models\Assignment;
use Domains\Shared\Models\User;
use Domains\Shared\Resources\DateResource;
use Domains\Shared\Resources\UserResource;
use Domains\TrackAttendant\Resources\IssueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Assignment $resource */
final class AssignmentResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'issue' => new IssueResource(
                resource: $this->whenLoaded(
                    relationship: 'issue',
                ),
            ),
            'gang_men' => UserResource::collection(
                User::whereIn('id', $this->resource->gang_men)->get(),
            ),
            'resolver' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'resolver',
                ),
            ),
            'created_at' => new DateResource(
                resource: $this->resource->created_at,
            ),
            'image_url' => $this->resource->image_url,
            'comment' => $this->resource->comment,

        ];
    }
}
