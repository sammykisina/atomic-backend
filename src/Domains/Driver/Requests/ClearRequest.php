<?php

declare(strict_types=1);

namespace Domains\Driver\Requests;

use Domains\Driver\Enums\AreaTypes;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ClearRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'area_id' => [
                'required',
                'integer',
            ],
            'type' => [
                'required',
                Rule::enum(AreaTypes::class),
            ],
        ];
    }
}
