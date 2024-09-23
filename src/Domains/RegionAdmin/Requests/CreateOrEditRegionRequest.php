<?php

declare(strict_types=1);

namespace Domains\RegionAdmin\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateOrEditRegionRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique(table: 'regions', column: 'name')->ignore($this->region ? $this->region->id : ''),
            ],
        ];
    }
}
