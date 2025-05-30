<x-mail::message>
# {{ __('mensajes.exito_mail') }}

{{ __('mensajes.hola_mail') }}, {{ $name }}!

{{ __('mensajes.registro_mail') }}

<x-mail::button :url="url('admin/login')" color="primary">
{{ __('mensajes.inicio_sesion_mail') }}
</x-mail::button>

{{ __('mensajes.gracias_mail') }},<br>
{{ __('mensajes.hunabku_mail') }}
{{-- {{ config('Hunabku') }} --}}
</x-mail::message>
