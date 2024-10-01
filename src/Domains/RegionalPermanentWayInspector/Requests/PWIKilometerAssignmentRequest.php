<?php

declare(strict_types=1);

namespace Domains\RegionalPermanentWayInspector\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class PWIKilometerAssignmentRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'line_id' => [
                'required',
                'exists:lines,id',
            ],
            'start_kilometer' => [
                'required',
                'numeric',
            ],
            'end_kilometer' => [
                'required',
                'numeric',
            ],
        ];
    }
}
