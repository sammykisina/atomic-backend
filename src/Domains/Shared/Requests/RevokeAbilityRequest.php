<?php

declare(strict_types=1);

namespace Domains\Shared\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class RevokeAbilityRequest extends FormRequest
{
    /**  @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'ability' => [
                'required',
                'string',
            ],
        ];
    }
}
