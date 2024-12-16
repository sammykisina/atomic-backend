<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditObcRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'serial_number' => [
                'required',
                Rule::unique(table: 'obcs', column: 'serial_number')->ignore(id: $this->obc ? $this->obc->id : ''),
            ],
        ];
    }
}
