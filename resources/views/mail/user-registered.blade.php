<x-mail::message>
# Registro Exitoso

Hola, {{ $name }}!

Te acabas de registrar exitosamente en nuestra plataforma.

<x-mail::button :url="url('admin/login')" color="primary">
Iniciar Sesión
</x-mail::button>

Gracias,<br>
Hunabku
{{-- {{ config('Hunabku') }} --}}
</x-mail::message>
