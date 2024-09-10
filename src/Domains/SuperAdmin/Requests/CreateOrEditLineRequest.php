<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditLineRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('lines', 'name')->ignore($this->line ? $this->line->id : ''),
            ],
            'regions' => [
                'array',
            ],
            'regions.*' => [
                'integer',
                'exists:regions,id',
            ],
            'counties' => [
                'array',
            ],
            'counties.*' => [
                'integer',
                'exists:counties,id',
            ],
        ];
    }
}
