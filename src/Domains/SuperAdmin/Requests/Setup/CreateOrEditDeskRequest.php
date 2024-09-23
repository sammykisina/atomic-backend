<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests\Setup;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditDeskRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>*/
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('desks', 'name')->ignore($this->desk ? $this->desk->id : ''),
            ],
        ];
    }
}
