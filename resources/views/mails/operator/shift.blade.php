<x-mail::message>

Hi {{ $shift->user->first_name . " " . $shift->user->last_name }},


@if ($type->value === 'CREATED')
A shift was created from {{ $shift->from }} to {{ $shift->to }},  {{ $shift->day }}.

You can view the details of the shift from your account and confirm the shift as soon as possible.
@elseif ($type->value === 'DELETED')
A shift created from {{ $shift->from }} to {{ $shift->to }}, {{ $shift->day }}was deleted.

You will be informed of new shift allocations incase of any.
@elseif ($type->value === 'EDITED')
A shift was edited. The updated shift details are from {{ $shift->from }} to {{ $shift->to }}, {{ $shift->day }}.

Please review the updated shift details at your earliest convenience.
@else
@endif


<strong>For further inquiries, please contact your system administrator.</strong>

Thanks,<br>

{{ config('app.name') }}
</x-mail::message>