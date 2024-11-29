<?php

declare(strict_types=1);

namespace Domains\SeniorEngineer\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RevertSpeedRestrictionRequest extends FormRequest
{
    /**  @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'reverted_speed' => [
                'required',
                'numeric',
                'min:0',
            ],
            'reverted_speed_comment' => [
                'required',
                'string',
            ],
        ];
    }
}
