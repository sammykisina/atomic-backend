<?php

declare(strict_types=1);

namespace Domains\Shared\Requests\Staff;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditDepartmentRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>*/
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('departments', 'name')->ignore($this->department ? $this->department->id : ''),
            ],
            'description' => [
                'required',
                'min:10',
            ],
        ];
    }
}
