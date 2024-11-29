<x-mail::message>

Hi {{ $inspectionSchedule->inspector->fullname}},


@if ($type->value === 'INSPECTION_SCHEDULE_CREATED')
An inspection schedule was created for line {{ $inspectionSchedule->line->name }} from {{ $inspectionSchedule->start_kilometer }} KM to {{ $inspectionSchedule->end_kilometer }} KM.

You can view the details of the inspection schedule from your account and confirm the schedule as soon as possible.
@elseif ($type->value === 'INSPECTION_SCHEDULE_DEACTIVATED')
An inspection schedule created for line {{ $inspectionSchedule->line->name }} from {{ $inspectionSchedule->start_kilometer }} KM to {{ $inspectionSchedule->end_kilometer }} KM was deactivated.

You will be informed of any new schedule allocations.
@else
@endif

<strong>For further inquiries, please contact your system administrator.</strong>

Thanks,<br>

{{ config('app.name') }}
</x-mail::message>