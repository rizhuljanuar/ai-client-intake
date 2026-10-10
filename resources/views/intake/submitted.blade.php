@extends('layouts.app')

@section('content')
    <section class="result-panel" aria-labelledby="submitted-title">
        <p class="eyebrow">Intake tersimpan</p>
        <h1 id="submitted-title">Informasi sudah diterima</h1>
        <p class="lede">
            Data awal sudah disimpan sebagai submission dan menunggu pemeriksaan reviewer.
            Halaman ini tidak menyatakan bahwa proyek sudah disetujui.
        </p>

        <dl class="submission-summary">
            <div>
                <dt>Nama kontak</dt>
                <dd>{{ $submission->contact_name_snapshot }}</dd>
            </div>
            <div>
                <dt>Status</dt>
                <dd>{{ $submission->status->value }}</dd>
            </div>
            <div>
                <dt>Masalah</dt>
                <dd>{{ $submission->projectBrief->problem_statement }}</dd>
            </div>
        </dl>

        <p class="page-action">
            <a class="button-link" href="{{ route('home') }}">Kembali ke halaman awal</a>
        </p>
    </section>
@endsection
