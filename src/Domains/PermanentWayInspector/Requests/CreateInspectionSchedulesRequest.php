<?php

declare(strict_types=1);

namespace Domains\PermanentWayInspector\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class CreateInspectionSchedulesRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'line_id' => [
                'required',
                'exists:lines,id',
            ],
            'inspection_schedules' => [
                'required',
                'array',
            ],
            'inspection_schedules.*.inspector_id' => [
                'required',
                'exists:users,id',
            ],
            'inspection_schedules.*.time' => [
                'required',
            ],
            'inspection_schedules.*.start_kilometer' => [
                'required',
                'numeric',
            ],
            'inspection_schedules.*.end_kilometer' => [
                'required',
                'numeric',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'line_id.exists' => 'The selected line is invalid or does not exits.',
        ];
    }
}
