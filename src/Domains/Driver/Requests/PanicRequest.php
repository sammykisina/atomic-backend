<?php

declare(strict_types=1);

namespace Domains\Driver\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class PanicRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'issue' => [
                'required',
                'string',
            ],
            'description' => [
                'required',
                'string',
            ],
            'latitude' => [
                'required',
            ],'longitude' => [
                'required',
            ],
        ];
    }
}
