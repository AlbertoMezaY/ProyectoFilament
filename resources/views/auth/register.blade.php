<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ __('mensajes.registro_form') }}</title>
    <link href="{{ asset('estilos.css') }}" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <h2>{{ __('mensajes.registro_usu_form') }}</h2>

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input name="name" placeholder={{ __('mensajes.nombre_form') }} value="{{ old('name') }}" required>
            <input name="apellidos" placeholder={{ __('mensajes.apellidos_form') }} value="{{ old('apellidos') }}" required>
            <input name="edad" type="number" placeholder={{ __('mensajes.edad_form') }} value="{{ old('edad') }}" required>
            <input name="email" type="email" placeholder={{ __('mensajes.correo_form') }} value="{{ old('email') }}" required>
            <input name="password" type="password" placeholder={{ __('mensajes.contrasena_form') }} required>
            <input name="password_confirmation" type="password" placeholder={{ __('mensajes.contrasena2_form') }} required>
            

            <button type="submit">{{ __('mensajes.reg_form') }}</button>
        </form>

        <a href="{{ url('/admin/login') }}">{{ __('mensajes.consulta_form') }}</a>
    </div>
</body>
</html>