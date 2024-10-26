<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests\ShiftManagement;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class GroupRequest extends FormRequest
{
    /**  @return array<string, ValidationRule|array<mixed>|string>  */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('groups', 'name')->ignore($this->group ? $this->group->id : ''),
            ],
            'description' => [
                'required',
                'string',
            ],
            'stations' => [
                'required',
                'array',
            ],
            'stations.*' => [
                'required',
                'exists:stations,id',
            ],
        ];
    }
}
