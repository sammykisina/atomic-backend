<?php

declare(strict_types=1);

namespace Domains\TrackAttendant\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StopInspectionRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'inspector_reached_origin' => [
                'required',
            ],
            'inspector_reached_destination' => [
                'required',
            ],
            'reason_for_abortion' => [
                'nullable',
                'string',
            ],
        ];
    }
}
