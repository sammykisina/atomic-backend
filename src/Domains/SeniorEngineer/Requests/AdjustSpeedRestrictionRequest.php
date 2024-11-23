<?php

declare(strict_types=1);

namespace Domains\SeniorEngineer\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class AdjustSpeedRestrictionRequest extends FormRequest
{
    /**
     * DETERMINE IF THE USER IS AUTHORIZED TO MAKE THIS REQUEST
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * GET THE VALIDATION RULES THAT APPLY TO THE REQUEST
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_kilometer' => [
                'required',
                'numeric',
            ],
            'end_kilometer' => [
                'required',
                'numeric',
            ],
            'start_kilometer_latitude' => [
                'required',
            ],
            'start_kilometer_longitude' => [
                'required',
            ],
            'end_kilometer_latitude' => [
                'required',
            ],
            'end_kilometer_longitude' => [
                'required',
            ],
            'speed' => [
                'required',
                'numeric',
            ],
        ];
    }
}
