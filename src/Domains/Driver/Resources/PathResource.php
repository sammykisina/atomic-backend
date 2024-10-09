<?php

declare(strict_types=1);

namespace Domains\Driver\Resources;

use Domains\Driver\Models\Path;
use Domains\SuperAdmin\Resources\LoopResource;
use Domains\SuperAdmin\Resources\SectionResource;
use Domains\SuperAdmin\Resources\StationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Path $resource */
final class PathResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'originStation' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'originStation',
                ),
            ),
            'section' => new SectionResource(
                resource: $this->whenLoaded(
                    relationship: 'section',
                ),
            ),
            'fromMainLine' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'fromMainLine',
                ),
            ),
            'fromLoop' => new LoopResource(
                resource: $this->whenLoaded(
                    relationship: 'fromLoop',
                ),
            ),
            'destinationStation' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'destinationStation',
                ),
            ),

            'toMainLine' => new StationResource(
                resource: $this->whenLoaded(
                    relationship: 'toMainLine',
                ),
            ),

            'toLoop' => new LoopResource(
                resource: $this->whenLoaded(
                    relationship: 'toLoop',
                ),
            ),
        ];
    }
}
