@extends('layouts.app')

@section('content')
    <section class="intro-panel" aria-labelledby="page-title">
        <p class="eyebrow">{{ $workflowStatus }}</p>
        <h1 id="page-title">{{ $appName }}</h1>
        <p class="lede">
            Tempat untuk mengumpulkan kebutuhan awal, menandai bagian yang belum jelas,
            dan menyiapkan scope review untuk pemeriksaan manusia.
        </p>

        <p class="page-action">
            <a class="button-link" href="{{ route('intake.create') }}">
                Isi formulir intake
            </a>
        </p>

        <div class="status-panel" aria-labelledby="status-title">
            <h2 id="status-title">Langkah berikutnya</h2>
            <p>{{ $nextStep }}</p>
        </div>

        <details class="notes-panel">
            <summary>Baca batas halaman awal</summary>
            <p>
                Halaman ini belum menerima data calon klien dan belum menjalankan AI.
                Kita akan menambahkan kedua bagian tersebut setelah struktur data dan kontraknya jelas.
            </p>
        </details>
    </section>
@endsection
