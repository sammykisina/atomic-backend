<?php

declare(strict_types=1);

namespace Domains\Shared\Resources;

use Domains\Shared\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Role $resource */
final class RoleResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'module_permissions' => PermissionResource::collection(
                resource: $this->whenLoaded(
                    relationship: 'permissions',
                ),
            ),
        ];
    }
}
