<?php

declare(strict_types=1);

namespace Domains\PermanentWayInspector\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class IssueAssignmentRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>*/
    public function rules(): array
    {
        return [
            'gang_men' => [
                'required',
                'array',
            ],
            'gang_men.*' => [
                'required',
                'exists:users,id',
            ],
        ];
    }
}
