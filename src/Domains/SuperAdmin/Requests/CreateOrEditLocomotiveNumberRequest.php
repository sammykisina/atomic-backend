<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditLocomotiveNumberRequest extends FormRequest
{
    /**  @return array<string, ValidationRule|array<mixed>|string>  */
    public function rules(): array
    {
        return [
            'number' => [
                'required',
                'string',
                Rule::unique('locomotive_numbers', 'number')->ignore($this->locomotiveNumber ? $this->locomotiveNumber->id : ''),
            ],
            'driver_id' => [
                'required',
                'exists:users,id',
            ],
        ];
    }
}
