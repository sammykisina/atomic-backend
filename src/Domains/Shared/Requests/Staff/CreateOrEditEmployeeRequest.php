<?php

declare(strict_types=1);

namespace Domains\Shared\Requests\Staff;

use Domains\Shared\Enums\UserTypes;
use Domains\Shared\Enums\WorkStatuses;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditEmployeeRequest extends FormRequest
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
                Rule::unique('users', 'email')->ignore($this->employee ? $this->employee->id : ''),
            ],
            'phone' => [
                'required',
                Rule::unique('users', 'phone')->ignore($this->employee ? $this->employee->id : ''),
            ],
            'employee_id' => [
                'required',
                'numeric',
                Rule::unique('users', 'employee_id')->ignore($this->employee ? $this->employee->id : ''),
            ],
            'national_id' => [
                'required',
                // 'integer',
                'size:8',
                Rule::unique('users', 'national_id')->ignore($this->employee ? $this->employee->id : ''),
            ],
            'type' => [
                'required',
                Rule::enum(UserTypes::class),
            ],
            'department_id' => [
                'required',
                'exists:departments,id',
            ],
            'region_id' => [
                'required',
                'exists:regions,id',
            ],
            'work_status' => [
                'required',
                Rule::enum(WorkStatuses::class),
            ],
        ];
    }

    /**   @return array<string, string> */
    public function messages(): array
    {
        return [
            'national_id.size' => 'National ID number must be 8 numbers.',
        ];
    }

}
