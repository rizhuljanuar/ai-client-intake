@extends('layouts.app')

@section('content')
    <section class="reviewer-panel" aria-labelledby="draft-page-title">
        <p class="eyebrow">Draft scope review</p>
        <h1 id="draft-page-title">
            {{ $submission->contact_name_snapshot }}
        </h1>
        <p class="lede">
            Draft ini membantu pemeriksaan awal. Reviewer tetap memeriksa isi terhadap submission asli.
        </p>

        <section class="source-panel" aria-labelledby="source-title">
            <h2 id="source-title">Sumber draft</h2>
            <dl class="detail-list">
                <div>
                    <dt>Status submission</dt>
                    <dd>{{ str_replace('_', ' ', ucfirst($submission->status->value)) }}</dd>
                </div>
                <div>
                    <dt>Masalah sumber</dt>
                    <dd>{{ $submission->projectBrief->problem_statement }}</dd>
                </div>
                <div>
                    <dt>Hasil yang diharapkan</dt>
                    <dd>{{ $submission->projectBrief->desired_outcome }}</dd>
                </div>
            </dl>
        </section>

        @if ($draft === null)
            <section class="empty-panel" role="status" aria-labelledby="draft-empty-title">
                <h2 id="draft-empty-title">Draft belum dibuat</h2>
                <p>
                    Submission ini belum memiliki hasil generation. Reviewer dapat memeriksa data sumber
                    sambil menunggu workflow generation tersedia.
                </p>
            </section>
        @elseif ($draft->status->value === 'processing')
            <section class="status-panel" role="status" aria-labelledby="draft-processing-title">
                <h2 id="draft-processing-title">Draft sedang diproses</h2>
                <p>
                    Worker sedang menyiapkan draft. Muat ulang halaman setelah worker menyelesaikan job.
                </p>
                <p class="muted-text">
                    Provider: {{ $draft->provider_mode ?: 'belum ditentukan' }}
                </p>
            </section>
        @elseif ($draft->status->value === 'generated')
            @include('reviewer.submissions.partials.generated-draft', [
                'draft' => $draft,
            ])
        @elseif (in_array($draft->status->value, ['failed_validation', 'failed'], true))
            <section class="error-panel" role="alert" aria-labelledby="draft-error-title">
                <h2 id="draft-error-title">Draft belum dapat ditampilkan</h2>
                <p>
                    Proses generation tidak menghasilkan draft yang dapat diperiksa.
                    Periksa log aplikasi atau jalankan kembali workflow sesuai kebijakan operator.
                </p>
                <p class="muted-text">
                    Kode proses: {{ $draft->error_code ?: 'unknown' }}
                </p>
            </section>
        @endif

        <p class="page-action">
            <a class="button-link" href="{{ route('reviewer.submissions.show', $submission) }}">
                Kembali ke detail submission
            </a>
        </p>
    </section>
@endsection
