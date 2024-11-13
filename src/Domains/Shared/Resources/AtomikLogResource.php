<?php

declare(strict_types=1);

namespace Domains\Shared\Resources;

use Domains\Shared\Models\AtomikLog;
use Domains\SuperAdmin\Resources\LocomotiveNumberResource;
use Domains\SuperAdmin\Resources\TrainResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read AtomikLog $resource */
final class AtomikLogResource extends JsonResource
{
    /**  @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'type' => $this->resource->type,
            'resourceble_type' => $this->getResourcebleType(
                $this->resource->resourceble_type,
            ) ,
            'resource' => $this->whenLoaded(
                relationship: 'resourceble',
            ),
            'actor' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'actor',
                ),
            ),
            'receiver' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'receiver',
                ),
            ),
            'created_at' => new DateResource(
                resource: $this->resource->created_at,
            ),
            'train' => new TrainResource(
                resource: $this->whenLoaded(
                    relationship: 'train',
                ),
            ),
            'current_location' => $this->resource->current_location,
            'locomotive' => new LocomotiveNumberResource(
                resource: $this->whenLoaded(
                    relationship: 'locomotive',
                )
            )
        ];
    }
}
