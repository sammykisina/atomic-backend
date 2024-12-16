<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Resources;

use Domains\SuperAdmin\Models\Obc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Obc $resource  */
final class ObcResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'serial_number' => $this->resource->serial_number,
        ];
    }
}
