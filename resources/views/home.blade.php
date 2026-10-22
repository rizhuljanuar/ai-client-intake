@extends('layouts.app')

@section('content')
    <section class="home-intro" aria-labelledby="page-title">
        <div class="home-intro-copy">
            <p class="eyebrow">{{ $workflowStatus }}</p>
            <h1 id="page-title">{{ $appName }}</h1>
            <p class="lede">
                Ubah kebutuhan awal calon klien menjadi bahan review yang bisa ditelusuri,
                tanpa mencampur data sumber dengan usulan AI.
            </p>

            <div class="home-actions">
                <a class="button-link" href="{{ route('intake.create') }}">Mulai intake</a>
                <a class="text-link" href="{{ route('reviewer.submissions.index') }}">Buka submission reviewer</a>
            </div>
        </div>

        <aside class="home-next-step" aria-labelledby="next-step-title">
            <p class="section-kicker">Sekarang</p>
            <h2 id="next-step-title">Langkah berikutnya</h2>
            <p>{{ $nextStep }}</p>
        </aside>
    </section>

    <section class="workflow-overview" aria-labelledby="workflow-title">
        <div class="section-heading home-section-heading">
            <div>
                <p class="section-kicker">Yang sudah tersedia</p>
                <h2 id="workflow-title">Satu alur, sumber data tetap jelas</h2>
            </div>
            <p class="section-description">Setiap tahap memiliki tempat dan batasnya sendiri.</p>
        </div>

        <ol class="workflow-list">
            <li>
                <span class="workflow-index">01</span>
                <div>
                    <h3>Data dari calon klien</h3>
                    <p>Isi intake disimpan sebagai sumber awal yang dapat dibaca reviewer.</p>
                </div>
                <a href="{{ route('intake.create') }}">Isi intake</a>
            </li>
            <li>
                <span class="workflow-index">02</span>
                <div>
                    <h3>Draft review</h3>
                    <p>Generation AI tampil sebagai usulan yang masih perlu diperiksa manusia.</p>
                </div>
                <a href="{{ route('reviewer.submissions.index') }}">Lihat submission</a>
            </li>
            <li>
                <span class="workflow-index">03</span>
                <div>
                    <h3>Klarifikasi dan risiko</h3>
                    <p>Workspace reviewer memisahkan pertanyaan, risiko, asumsi, dan candidate.</p>
                </div>
                <a href="{{ route('reviewer.submissions.index') }}">Buka workspace</a>
            </li>
        </ol>
    </section>

@endsection
