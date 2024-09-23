<x-mail::message>

Hi {{ $shift->user->first_name . " " . $shift->user->last_name }},


@if ($type->value === 'CREATED')
A shift was created from {{ $shift->from }} to {{ $shift->to }} {{ $shift->day }} on {{ $shift->startStation->name }} - {{ $shift->endStation->name }}.

You can view the details of the shift from your account and confirm the shift as soon as possible.
@elseif ($type->value === 'DELETED')
A shift created from {{ $shift->from }} to {{ $shift->to }} {{ $shift->day }} on {{ $shift->startStation->name }} - {{ $shift->endStation->name }} was deleted.

You will be informed of any new shift allocations incase of any.
@else
@endif

<strong>For further inquiries, please contact your system administrator.</strong>

Thanks,<br>

{{ config('app.name') }}
</x-mail::message>