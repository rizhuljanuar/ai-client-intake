@extends('layouts.app')

@section('content')
    <section class="review-workspace" aria-labelledby="review-workspace-title">
        <p class="eyebrow">Workspace reviewer</p>
        <h1 id="review-workspace-title">{{ $submission->contact_name_snapshot }}</h1>
        <p class="lede">
            Bandingkan data sumber, hasil generation, dan perubahan reviewer sebelum mengambil keputusan.
        </p>

        <section class="provenance-banner" aria-labelledby="active-source-title">
            <h2 id="active-source-title">Sumber yang sedang ditampilkan</h2>
            @if ($activeRevision)
                <dl class="provenance-list">
                    <div>
                        <dt>Revision</dt>
                        <dd>{{ $activeRevision->revision_number }}</dd>
                    </div>
                    <div>
                        <dt>Jenis perubahan</dt>
                        <dd>{{ $activeRevision->source_type }}</dd>
                    </div>
                    <div>
                        <dt>Parent</dt>
                        <dd>{{ $activeRevision->parent_revision_id ?: 'Tidak ada' }}</dd>
                    </div>
                </dl>
            @elseif ($submission->scopeReviewDraft)
                <p>Generation AI tersedia. Belum ada edit reviewer yang menjadi revision aktif.</p>
                <p class="muted-text">
                    Kontrak {{ $submission->scopeReviewDraft->contract_version }}
                    · Provider {{ $submission->scopeReviewDraft->provider_mode }}
                </p>
            @else
                <p>Belum ada draft atau revision yang dapat ditampilkan.</p>
            @endif
        </section>

        <section class="source-panel" aria-labelledby="source-data-title">
            <h2 id="source-data-title">Data dari calon klien</h2>
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
            </dl>
        </section>

        <section class="review-section" aria-labelledby="questions-title">
            <div class="section-heading">
                <div>
                    <p class="source-label source-label-reviewer">Workflow reviewer</p>
                    <h2 id="questions-title">Pertanyaan klarifikasi</h2>
                </div>
                <span class="count-label">{{ $questions->count() }} item</span>
            </div>
            @if ($questions->isEmpty())
                <p class="empty-inline">Belum ada pertanyaan yang dipilih untuk workflow.</p>
            @else
                <ul class="review-list">
                    @foreach ($questions as $question)
                        <li>
                            <div class="item-heading">
                                <strong>{{ $question->question }}</strong>
                                <span class="status-label">{{ $question->status }}</span>
                            </div>
                            <p class="source-label">Sumber: {{ $question->source_type }}</p>
                            @if ($question->answers->isNotEmpty())
                                <p><b>Jawaban terbaru:</b> {{ $question->answers->last()->answer }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="review-section" aria-labelledby="risks-title">
            <div class="section-heading">
                <div>
                    <p class="source-label source-label-ai">Analisis dan penilaian</p>
                    <h2 id="risks-title">Risiko</h2>
                </div>
                <span class="count-label">{{ $risks->count() }} item</span>
            </div>
            @if ($risks->isEmpty())
                <p class="empty-inline">Belum ada risiko yang disimpan sebagai item workflow.</p>
            @else
                <ul class="review-list">
                    @foreach ($risks as $risk)
                        <li>
                            <div class="item-heading">
                                <strong>{{ $risk->title }}</strong>
                                <span class="severity severity-{{ $risk->severity }}">
                                    {{ $risk->severity }}
                                </span>
                            </div>
                            <p>{{ $risk->description }}</p>
                            <p class="source-label">Sumber: {{ $risk->source_type }}</p>
                            <p class="status-label">Status: {{ $risk->status }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="review-section" aria-labelledby="assumptions-title">
            <div class="section-heading">
                <div>
                    <p class="source-label source-label-reviewer">Hal yang perlu dikonfirmasi</p>
                    <h2 id="assumptions-title">Asumsi</h2>
                </div>
                <span class="count-label">{{ $assumptions->count() }} item</span>
            </div>
            @if ($assumptions->isEmpty())
                <p class="empty-inline">Belum ada asumsi yang disimpan sebagai item workflow.</p>
            @else
                <ul class="review-list">
                    @foreach ($assumptions as $assumption)
                        <li>
                            <strong>{{ $assumption->statement }}</strong>
                            <p class="source-label">Sumber: {{ $assumption->source_type }}</p>
                            <p class="status-label">Status: {{ $assumption->status }}</p>
                            @if ($assumption->needs_confirmation)
                                <p class="needs-confirmation">Perlu dikonfirmasi.</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="review-section" aria-labelledby="candidates-title">
            <div class="section-heading">
                <div>
                    <p class="source-label source-label-ai">Hasil regenerasi</p>
                    <h2 id="candidates-title">Candidate yang menunggu pilihan</h2>
                </div>
                <span class="count-label">{{ $candidates->count() }} item</span>
            </div>
            @if ($candidates->isEmpty())
                <p class="empty-inline">Belum ada candidate yang menunggu perbandingan.</p>
            @else
                <ul class="review-list">
                    @foreach ($candidates as $candidate)
                        <li>
                            <div class="item-heading">
                                <strong>Candidate {{ $candidate->id }}</strong>
                                <span class="status-label">{{ $candidate->status }}</span>
                            </div>
                            <p>Base revision: {{ $candidate->base_revision_id }}</p>
                            <p class="source-label">Regenerasi: {{ $candidate->requested_reason }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <p class="review-warning">
            Halaman ini menjelaskan sumber dan status data. Ia belum menyediakan approve, reject, merge,
            atau discard karena endpoint dan authorization belum selesai.
        </p>
    </section>
@endsection
