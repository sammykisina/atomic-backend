<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests\ShiftManagement;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class GroupRequest extends FormRequest
{
    /**  @return array<string, ValidationRule|array<mixed>|string>  */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'unique:groups,name',
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
