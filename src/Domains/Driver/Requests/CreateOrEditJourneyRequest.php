<?php

declare(strict_types=1);

namespace Domains\Driver\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class CreateOrEditJourneyRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'train_id' => [
                'required',
                'exists:trains,id',
            ],

            'requesting_location' => ['required', 'array'],
            'requesting_location.id' => ['required', 'integer'],
            'requesting_location.type' => ['required', 'in:STATION,LOOP,SECTION'],
            'length' => [
                'required',
                'integer',
            ],
        ];
    }
}
