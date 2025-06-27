<?php

declare(strict_types=1);

namespace Domains\SuperAdmin\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class CreateOrEditStationRequest extends FormRequest
{
    /**  @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],
            'start_kilometer' => [
                'numeric',
                'required',
            ],
            'end_kilometer' => [
                'numeric',
                'required',
            ] ,

            'start_latitude_top' => [
                'numeric',
                'required',
            ] ,
            'start_longitude_top' => [
                'numeric',
                'required',
            ],
            'start_latitude_bottom' => [
                'numeric',
                'required',
            ] ,
            'start_longitude_bottom' => [
                'numeric',
                'required',
            ],
            'end_latitude_top' => [
                'numeric',
                'required',
            ] ,
            'end_longitude_top' => [
                'numeric',
                'required',
            ],
            'end_latitude_bottom' => [
                'numeric',
                'required',
            ] ,
            'end_longitude_bottom' => [
                'numeric',
                'required',
            ],

            'line_id' => [
                'required',
                'exists:lines,id',
            ],
            'is_yard' => [
                'required',
                'boolean',
            ],
            'position_from_line' => [
                'required',
                'numeric',
            ],
            'geofence_name' => [ 'nullable']
        ];
    }
}
