<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{__('blog.titulo_blog')}}</title>
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
    {{--<script src="https://cdn.tailwindcss.com"></script>--}}

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
        <h1>{{__('blog.titulo_blog')}}</h1>
        <div class="blog-list">
            @forelse ($blogs as $blog)
                <div class="blog-card">
                    @if ($blog->imagen)
                        <img src="{{ asset('storage/' . $blog->imagen) }}" alt="{{ $blog->titulo }}">
                    @endif
                    <div class="blog-card-content">
                        <h2>
                            <a href="{{ route('blog.show', $blog->slug) }}">
                                {{ $blog->titulo }}
                            </a>
                        </h2>
                        <p class="meta">{{__('blog.publicado_el')}} {{ $blog->created_at->format('d/m/Y') }} {{__('blog.por')}} {{ $blog->user->name }}</p>
                        <p>{{ Str::limit(strip_tags($blog->contenido), 100) }}</p>
                    </div>
                </div>
            @empty
                <p>{{__('blog.no_blog')}}</p>
            @endforelse
        </div>
        <!-- Paginación -->
        <div class="custom-pagination">
            {{ $blogs->links('vendor.pagination.blog') }}
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>{{__('blog.simbolo_derechos')}} {{ date('Y') }} {{__('blog.mensaje_derechos')}}</p>
    </footer>
</body>
</html>