<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests\ShiftManagement;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class CreateOrEditShiftRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'desk_id' => [
                'required',
                'exists:desks,id',
            ],
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'day' => [
                'required',
                'date',
            ],
            'from' => [
                'required',
                'date_format:H:i',
            ],
            'to' => [
                'required',
                'date_format:H:i',
            ],
        ];
    }
}
