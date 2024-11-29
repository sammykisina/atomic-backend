<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests\ShiftManagement;

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
                Rule::unique('desks', 'name')
                    ->where('group_id', $this->group_id)
                    ->ignore($this->desk ? $this->desk->id : ''),
            ],
            'group_id' => [
                'required',
                Rule::unique('desks', 'group_id')
                    ->where('name', $this->name)
                    ->ignore($this->desk ? $this->desk->id : ''),
            ],
        ];
    }
}
