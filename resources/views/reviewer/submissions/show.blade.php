@extends('layouts.app')

@section('content')
    <section class="reviewer-panel" aria-labelledby="submission-detail-title">
        <p class="eyebrow">Detail submission</p>
        <h1 id="submission-detail-title">{{ $submission->contact_name_snapshot }}</h1>
        <p class="lede">
            Data ini adalah intake awal. Reviewer tetap perlu memeriksa isinya sebelum membuat keputusan.
        </p>

        <div class="detail-grid">
            <section class="detail-block" aria-labelledby="contact-title">
                <h2 id="contact-title">Kontak</h2>
                <dl class="detail-list">
                    <div>
                        <dt>Nama</dt>
                        <dd>{{ $submission->contact_name_snapshot }}</dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $submission->contact_email_snapshot }}</dd>
                    </div>
                    <div>
                        <dt>Organisasi</dt>
                        <dd>{{ $submission->organization_name_snapshot ?: 'Tidak diisi' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="detail-block" aria-labelledby="status-title">
                <h2 id="status-title">Status workflow</h2>
                <dl class="detail-list">
                    <div>
                        <dt>Status saat ini</dt>
                        <dd>{{ str_replace('_', ' ', ucfirst($submission->status->value)) }}</dd>
                    </div>
                    <div>
                        <dt>Dikirim</dt>
                        <dd>{{ $submission->submitted_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <section class="detail-block" aria-labelledby="brief-title">
            <h2 id="brief-title">Kebutuhan proyek</h2>
            <dl class="detail-list">
                <div>
                    <dt>Masalah</dt>
                    <dd>{{ $submission->projectBrief->problem_statement }}</dd>
                </div>
                <div>
                    <dt>Hasil yang diharapkan</dt>
                    <dd>{{ $submission->projectBrief->desired_outcome }}</dd>
                </div>
                @if ($submission->projectBrief->constraints)
                    <div>
                        <dt>Batasan</dt>
                        <dd>{{ $submission->projectBrief->constraints }}</dd>
                    </div>
                @endif
                @if ($submission->projectBrief->additional_context)
                    <div>
                        <dt>Konteks tambahan</dt>
                        <dd>{{ $submission->projectBrief->additional_context }}</dd>
                    </div>
                @endif
            </dl>
        </section>

        <section class="detail-block" aria-labelledby="history-title">
            <h2 id="history-title">Riwayat status</h2>
            @if ($submission->statusHistories->isEmpty())
                <p>Belum ada riwayat status yang dapat ditampilkan.</p>
            @else
                <ol class="history-list">
                    @foreach ($submission->statusHistories as $history)
                        <li>
                            <strong>{{ str_replace('_', ' ', ucfirst($history->to_status->value)) }}</strong>
                            <span>{{ $history->created_at->format('Y-m-d H:i') }}</span>
                            @if ($history->reason)
                                <p>{{ $history->reason }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        <p class="page-action">
            <a class="button-link" href="{{ route('reviewer.submissions.index') }}">
                Kembali ke daftar
            </a>
        </p>
    </section>
@endsection
