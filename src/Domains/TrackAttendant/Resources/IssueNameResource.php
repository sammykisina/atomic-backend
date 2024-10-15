<?php

declare(strict_types=1);

namespace Domains\TrackAttendant\Resources;

use Domains\TrackAttendant\Models\IssueName;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read IssueName $resource */
final class IssueNameResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
        ];
    }
}
