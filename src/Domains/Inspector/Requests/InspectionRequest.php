<?php

declare(strict_types=1);

namespace Domains\Inspector\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class InspectionRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'start_time' => [
                'required',
                'string',
            ],
        ];
    }
}
