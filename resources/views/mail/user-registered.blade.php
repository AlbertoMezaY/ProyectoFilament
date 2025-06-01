<x-mail::message>
# {{ __('mail.exito_mail') }}

{{ __('mail.hola_mail') }}, {{ $name }}!

{{ __('mail.registro_mail') }}

<x-mail::button :url="url('admin/login')" color="primary">
{{ __('mail.inicio_sesion_mail') }}
</x-mail::button>

{{ __('mail.gracias_mail') }},<br>
{{ __('mail.hunabku_mail') }}
{{-- {{ config('Hunabku') }} --}}
</x-mail::message>
