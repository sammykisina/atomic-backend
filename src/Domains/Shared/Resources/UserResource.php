<?php

declare(strict_types=1);

namespace Domains\Shared\Resources;

use Domains\Shared\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read User $resource */

final class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'image_url' => $this->resource->image_url,
            'type' => $this->resource->type,
            'is_admin' => $this->resource->is_admin,
            'status' => $this->resource->status,
            'work_status' => $this->resource->work_status,
            'department' => new DepartmentResource(
                resource: $this->whenLoaded(
                    relationship: 'department',
                ),
            ),
            'region' => new RegionResource(
                resource: $this->whenLoaded(
                    relationship: 'region',
                ),
            ),
        ];
    }
}
