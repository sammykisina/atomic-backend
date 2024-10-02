<?php

declare(strict_types=1);

namespace Domains\Inspector\Resources;

use Domains\Inspector\Models\Issue;
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
        ];
    }
}
