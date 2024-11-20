<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class UnauthorizedAreaEntryNotificationRequest extends FormRequest
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
            'type' => [
                'required',
                'in:AREA_LICENSE_TO_ANOTHER,AREA_NOT_LICENSED',
            ],
        ];
    }
}
