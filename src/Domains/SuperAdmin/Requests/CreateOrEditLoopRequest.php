<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests;

use Domains\SuperAdmin\Enums\LoopPositions;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditLoopRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>  */
    public function rules(): array
    {
        return [
            'distance' => [
                'required',
                'numeric',
            ],
            'position' => [
                'required',
                Rule::enum(LoopPositions::class),
                Rule::unique(
                    table: 'loops',
                    column: 'position',
                )->where(
                    column: 'line_id',
                    value: $this->line->id,
                )->where(
                    column: 'station_id',
                    value: $this->station->id,
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'position' => 'This loop already exists.',
        ];
    }
}
