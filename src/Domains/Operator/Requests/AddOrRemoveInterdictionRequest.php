<?php

declare(strict_types=1);

namespace Domains\Operator\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AddOrRemoveInterdictionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'numeric',
            ],
            'type' => [
                'required',
                'in:LOOP,SECTION,STATION',
            ],
        ];
    }
}
