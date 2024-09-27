<?php

declare(strict_types=1);

namespace Domains\ReginalCivilEngineer\Requests;

use Domains\ReginalCivilEngineer\Enums\InspectionScheduleStatuses;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class EditInspectionScheduleRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string>*/
    public function rules(): array
    {
        return [
            'time' => [
                'required',
            ],
            'start_kilometer' => [
                'required',
                'numeric',
            ],
            'end_kilometer' => [
                'required',
                'numeric',
            ],
            'status' => [
                'required',
                Rule::enum(InspectionScheduleStatuses::class),
            ],
        ];
    }
}
