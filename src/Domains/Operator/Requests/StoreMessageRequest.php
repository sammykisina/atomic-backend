<?php

declare(strict_types=1);

namespace Domains\Operator\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StoreMessageRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'locomotive_id' =>  [
                'required',
                'exists:locomotive_numbers,id',
            ],
            'message' =>  [
                'required',
                'string',
            ],
        ];
    }
}
