<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $blog->titulo }}</title>
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="container">
            <div class="logo">
                {{--<img src="{{ asset('images/logo.png') }}" alt="Hunabku Logo">--}}
            </div>
            <ul>
                <li><a href="{{ route('blog.index') }}" class="active">{{__('blog.titulo_blog')}}</a></li>
            </ul>
            <div class="search">
                <input type="text" placeholder="Buscar...">
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <div class="content-wrapper">
            <div class="main-content">
                <h1>{{ $blog->titulo }}</h1>
                @if ($blog->imagen)
                    <img src="{{ asset('storage/' . $blog->imagen) }}" alt="{{ $blog->titulo }}" class="blog-image">
                @endif
                <div class="blog-content">
                    {!! $blog->contenido !!}
                </div>
            </div>
            <!-- Tarjeta de Categorías y Etiquetas -->
            <div class="sidebar">
                <div class="widget categories-tags-card">
                    <h3>{{__('blog.categoria')}}</h3>
                    <ul>
                        @foreach ($blog->categories as $category)
                            <li>
                                <a href="{{ route('blog.filter.category', $category->name) }}" class="category-link">
                                    {{ $category->name }} ({{ $categoryCounts }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <h3 class="mt-4">{{__('blog.etiqueta')}}</h3>
                    <ul>
                        @foreach ($blog->tags as $tag)
                            <li>
                                <a href="{{ route('blog.filter.tag', $tag->name) }}" class="tag-link">
                                    {{ $tag->name }} ({{ $tagCounts }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>{{__('blog.simbolo_derechos')}} {{ date('Y') }} {{__('blog.mensaje_derechos')}}</p>
    </footer>
</body>
</html>