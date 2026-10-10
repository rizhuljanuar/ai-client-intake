<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submission tidak ditemukan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="page-shell">
        <section class="error-page" aria-labelledby="not-found-title">
            <p class="eyebrow">Data tidak ditemukan</p>
            <h1 id="not-found-title">Submission itu tidak tersedia</h1>
            <p>
                ID yang kamu buka tidak cocok dengan submission yang dapat dibaca aplikasi.
            </p>
            <a class="button-link" href="{{ route('reviewer.submissions.index') }}">
                Kembali ke daftar submission
            </a>
        </section>
    </main>
</body>
</html>
