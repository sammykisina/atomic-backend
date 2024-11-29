<?php

declare(strict_types=1);

namespace Domains\Shared\Resources;

use Domains\Shared\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property-read  Otp $resource */
final class OtpResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'type' => $this->resource->type,
            'code' => $this->resource->code,
        ];
    }
}
