<?php

declare(strict_types=1);

namespace Domains\SeniorEngineer\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class ApproveSpeedRestrictionRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'end_kilometer' => [
                'required',
            ],
            'end_kilometer_latitude' => [
                'required',
            ],
            'end_kilometer_longitude' => [
                'required',
            ],
        ];
    }
}
