<section class="draft-content" aria-labelledby="generated-title">
    <div class="draft-heading">
        <div>
            <p class="eyebrow">Draft siap diperiksa</p>
            <h2 id="generated-title">Hasil analisis awal</h2>
        </div>
        <p class="draft-meta">
            Kontrak {{ $draft->contract_version }}
            · Provider {{ $draft->provider_mode }}
        </p>
    </div>

    <div class="review-warning" role="note">
        <strong>Periksa sebelum dipakai.</strong>
        Isi di bawah berasal dari proses generation dan belum menjadi keputusan scope.
    </div>

    <section class="draft-block" aria-labelledby="summary-title">
        <h3 id="summary-title">Ringkasan</h3>
        <p>{{ $draft->draft_payload['summary'] }}</p>
    </section>

    <section class="draft-block" aria-labelledby="unclear-title">
        <h3 id="unclear-title">Bagian yang belum jelas</h3>
        @if (count($draft->draft_payload['unclear_items']) === 0)
            <p class="muted-text">Tidak ada bagian yang ditandai oleh provider.</p>
        @else
            <ul class="review-list">
                @foreach ($draft->draft_payload['unclear_items'] as $item)
                    <li>
                        <strong>{{ $item['statement'] }}</strong>
                        <p>{{ $item['impact'] }}</p>
                        <p><b>Pertanyaan:</b> {{ $item['question'] }}</p>
                        <small>Sumber: {{ implode(', ', $item['source_fields']) }}</small>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="draft-block" aria-labelledby="risks-title">
        <h3 id="risks-title">Risiko</h3>
        @if (count($draft->draft_payload['risks']) === 0)
            <p class="muted-text">Tidak ada risiko yang ditandai oleh provider.</p>
        @else
            <ul class="review-list">
                @foreach ($draft->draft_payload['risks'] as $risk)
                    <li>
                        <div class="item-heading">
                            <strong>{{ $risk['title'] }}</strong>
                            <span class="severity severity-{{ $risk['severity'] }}">
                                {{ $risk['severity'] }}
                            </span>
                        </div>
                        <p>{{ $risk['description'] }}</p>
                        <p><b>Langkah pengurangan:</b> {{ $risk['mitigation'] }}</p>
                        <small>Sumber: {{ implode(', ', $risk['source_fields']) ?: 'Tidak ditentukan' }}</small>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="draft-block" aria-labelledby="assumptions-title">
        <h3 id="assumptions-title">Asumsi</h3>
        @if (count($draft->draft_payload['assumptions']) === 0)
            <p class="muted-text">Tidak ada asumsi yang ditandai oleh provider.</p>
        @else
            <ul class="review-list">
                @foreach ($draft->draft_payload['assumptions'] as $assumption)
                    <li>
                        <strong>{{ $assumption['statement'] }}</strong>
                        @if ($assumption['needs_confirmation'])
                            <p class="needs-confirmation">Perlu dikonfirmasi reviewer.</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="draft-block" aria-labelledby="questions-title">
        <h3 id="questions-title">Pertanyaan lanjutan</h3>
        @if (count($draft->draft_payload['follow_up_questions']) === 0)
            <p class="muted-text">Tidak ada pertanyaan lanjutan dari provider.</p>
        @else
            <ol class="review-list">
                @foreach ($draft->draft_payload['follow_up_questions'] as $question)
                    <li>
                        <strong>{{ $question['question'] }}</strong>
                        <p>Prioritas: {{ $question['priority'] }}</p>
                    </li>
                @endforeach
            </ol>
        @endif
    </section>

    <p class="review-warning">
        Draft ini belum dapat disetujui atau ditolak. Periksa sumber dan hasil analisis sebelum keputusan
        manusia dicatat.
    </p>
</section>
