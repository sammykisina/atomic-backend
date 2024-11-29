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
            'receiver_id' =>  [
                'required',
                'exists:users,id',
            ],
            'message' =>  [
                'required',
                'string',
            ],
        ];
    }
}
