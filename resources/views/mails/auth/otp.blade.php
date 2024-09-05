<x-mail::message>

Hi {{ $user->first_name . " " . $user->last_name }},

Your 6-digit code is:

<strong>{{ $otp->code }}</strong>

@if ($otp->type === 'password-reset')
<p>Use this code to reset your password in the app.</p>
@else
<p>Use this code to complete your verification in the app.</p>
@endif

Do not share this code.Atomic representatives will not reach out to you to verify this code over SMS.

<strong>The code is valid for 10 minutes.</strong>

Thanks,<br>

{{ config('app.name') }}
</x-mail::message>