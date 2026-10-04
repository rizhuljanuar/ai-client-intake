<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'AI Client Intake' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <header class="site-header">
            <a class="brand" href="{{ route('home') }}">AI Client Intake</a>
        </header>
    
        <main class="page-shell">
            @yield('content')
        </main>
    </body>
</html>

