<?php

declare(strict_types=1);

namespace Domains\SeniorTrackInspector\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class SpeedRestrictionSuggestionRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'speed_suggestion' => [
                'required',
                'numeric',
                'min:0',
            ],
            'speed_suggestion_comment' => [
                'required',
                'string',
            ],
        ];
    }
}
