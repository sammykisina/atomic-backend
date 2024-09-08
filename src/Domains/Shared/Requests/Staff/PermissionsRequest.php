<?php

declare(strict_types=1);

namespace Domains\Shared\Requests\Staff;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class PermissionsRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>*/
    public function rules(): array
    {
        return [
            'abilities' => [
                'array',
            ],
            'abilities.*.module' => [
                'required',
                'string',
            ],
            'abilities.*.permissions' => [
                'array',
            ],
            'permissions.*' => [
                'string',
                'required',
            ],
        ];
    }
}
