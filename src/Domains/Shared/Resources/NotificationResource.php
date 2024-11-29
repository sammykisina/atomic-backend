<?php

declare(strict_types=1);

namespace Domains\Shared\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class NotificationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => new DateResource(
                resource: $this->created_at,
            ),
            'data' => $this->data,
            'read_at' =>  new DateResource(
                resource: $this->read_at,
            ),
        ];
    }
}
