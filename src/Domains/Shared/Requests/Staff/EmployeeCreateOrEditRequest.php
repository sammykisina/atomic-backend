<?php

declare(strict_types=1);

namespace Domains\Shared\Requests\Staff;

use Domains\Shared\Enums\ModelStatuses;
use Domains\Shared\Enums\WorkStatuses;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class EmployeeCreateOrEditRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
            ],
            'last_name' => [
                'required',
                'string',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user ? $this->user->id : ''),
            ],
            'phone' => [
                'required',
                Rule::unique('users', 'phone')->ignore($this->user ? $this->user->id : ''),
            ],
            'employee_id' => [
                'required',
                Rule::unique('users', 'employee_id')->ignore($this->user ? $this->user->id : ''),
            ],
            'national_id' => [
                'required',
                Rule::unique('users', 'national_id')->ignore($this->user ? $this->user->id : ''),
            ],
            'type' => [
                'required',
                Rule::enum(ModelStatuses::class),
            ],
            'department_id' => [
                'required',
                'exists:departments,id',
            ],
            'region_id' => [
                'required',
                'exists:region,id',
            ],
            'work_status' => [
                'required',
                Rule::enum(WorkStatuses::class),
            ],
        ];
    }
}
