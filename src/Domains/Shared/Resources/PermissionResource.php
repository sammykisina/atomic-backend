<?php

declare(strict_types=1);

namespace Domains\Shared\Resources;

use Domains\Shared\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  Permission $resource */
final class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'module' => $this->resource->module,
            'abilities' => $this->resource->abilities,
        ];
    }
}
