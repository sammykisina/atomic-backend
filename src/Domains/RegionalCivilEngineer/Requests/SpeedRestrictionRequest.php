<?php

declare(strict_types=1);

namespace Domains\RegionalCivilEngineer\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SpeedRestrictionRequest extends FormRequest
{
    /**  @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'proposed_speed' => [
                'required',
                'numeric',
                'min:0',
            ],
            'proposed_speed_comment' => [
                'required',
                'string',
            ],
        ];
    }
}
