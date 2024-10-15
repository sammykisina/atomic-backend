<?php

declare(strict_types=1);

namespace Domains\TrackAttendant\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditIssueNameRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('issue_names', 'name'),
            ],
        ];
    }
}
