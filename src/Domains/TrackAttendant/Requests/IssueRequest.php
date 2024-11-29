<?php

declare(strict_types=1);

namespace Domains\TrackAttendant\Requests;

use Domains\TrackAttendant\Enums\IssueConditions;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class IssueRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>*/
    public function rules(): array
    {
        return [
            'condition' => [
                'required',
                Rule::enum(type: IssueConditions::class),
            ],
            'description' => [
                'required',
                'string',
            ],
            'image_url' => [
                'required',
                'string',
            ],

            'latitude' => [
                'required',
                'numeric',
            ],
            'longitude' => [
                'required',
                'numeric',
            ],
            'issue_name_id' => [
                'required',
                'exists:issue_names,id',
            ],
            'issue_kilometer' => [
                'required',
            ],
        ];
    }
}
