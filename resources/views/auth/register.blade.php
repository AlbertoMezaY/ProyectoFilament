<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="{{ asset('estilos.css') }}" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <h2>Registro de Usuario</h2>

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
            <input name="name" placeholder="Nombre" value="{{ old('name') }}" required>
            <input name="apellidos" placeholder="Apellidos" value="{{ old('apellidos') }}" required>
            <input name="edad" type="number" placeholder="Edad" value="{{ old('edad') }}" required>
            <input name="email" type="email" placeholder="Correo electrónico" value="{{ old('email') }}" required>
            <input name="password" type="password" placeholder="Contraseña" required>
            <input name="password_confirmation" type="password" placeholder="Confirmar Contraseña" required>
            

            <button type="submit">Registrarse</button>
        </form>

        <a href="{{ url('/admin/login') }}">¿Ya tienes una cuenta? Inicia sesión</a>
    </div>
</body>
</html>