<?php

declare(strict_types=1);

namespace Domains\PermanentWayInspector\Resources;

use Domains\Inspector\Resources\IssueResource;
use Domains\PermanentWayInspector\Models\Assignment;
use Domains\Shared\Models\User;
use Domains\Shared\Resources\UserResource;
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

        ];
    }
}
