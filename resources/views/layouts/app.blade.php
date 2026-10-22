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
            <div class="header-inner">
                <a class="brand" href="{{ route('home') }}">AI Client Intake</a>
                <nav class="site-nav" aria-label="Navigasi utama">
                    <a href="{{ route('home') }}">Beranda</a>
                    <a href="{{ route('intake.create') }}">Mulai intake</a>
                    <a href="{{ route('reviewer.submissions.index') }}">Submission reviewer</a>
                </nav>
            </div>
        </header>
    
        <main class="page-shell">
            @yield('content')
        </main>
    </body>
</html>

