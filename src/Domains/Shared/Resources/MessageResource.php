<?php

declare(strict_types=1);

namespace Domains\Shared\Resources;

use Domains\Shared\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read Message $resource */
final class MessageResource extends JsonResource
{
    /**  @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'message' => $this->resource->message,
            'sender' => new UserResource(
                resource: $this->whenLoaded(
                    relationship: 'sender',
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

        ];
    }
}
