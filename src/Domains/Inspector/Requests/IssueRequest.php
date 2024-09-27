<?php

declare(strict_types=1);

namespace Domains\Inspector\Requests;

use Domains\Inspector\Enums\IssueConditions;
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
                'required_if:condition,CRITICAL',
            ],
            'image_url' => [
                'required_if:condition,CRITICAL',
            ],
            'latitude' => [
                'required',
                'numeric',
            ],
            'longitude' => [
                'required',
                'numeric',
            ],
        ];
    }
}
