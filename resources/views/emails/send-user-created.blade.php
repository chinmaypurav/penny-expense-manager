@component('mail::message')
# Hi {{ $name }}, welcome to {{ config('app.name') }}

@component('mail::button', ['url' => route('filament.app.auth.login')])
Login
@endcomponent

Please use the following password to log in:
@component('mail::panel')
    {{ $password }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
