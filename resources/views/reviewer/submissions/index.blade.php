@extends('layouts.app')

@section('content')
    <section class="reviewer-panel" aria-labelledby="submission-list-title">
        <p class="eyebrow">Area reviewer</p>
        <h1 id="submission-list-title">Submission intake</h1>
        <p class="lede">
            Baca informasi awal calon klien sebelum draft scope review dibuat.
        </p>

        <form method="GET" action="{{ route('reviewer.submissions.index') }}" class="filter-form">
            <label for="status">Filter status</label>
            <select id="status" name="status">
                <option value="">Semua status</option>
                @foreach ($statuses as $status)
                    <option
                        value="{{ $status->value }}"
                        @selected($selectedStatus === $status)
                    >
                        {{ str_replace('_', ' ', ucfirst($status->value)) }}
                    </option>
                @endforeach
            </select>
            <button class="secondary-button" type="submit">Terapkan filter</button>
        </form>

        @if ($submissions->isEmpty())
            <div class="empty-panel" role="status">
                <h2>Belum ada submission yang cocok</h2>
                <p>
                    Coba hapus filter atau tunggu sampai calon klien mengirim formulir intake.
                </p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <caption>Submission yang perlu diperiksa</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kontak</th>
                            <th scope="col">Organisasi</th>
                            <th scope="col">Status</th>
                            <th scope="col">Dikirim</th>
                            <th scope="col"><span class="sr-only">Tindakan</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($submissions as $submission)
                            <tr>
                                <td>{{ $submission->contact_name_snapshot }}</td>
                                <td>{{ $submission->organization_name_snapshot ?: 'Tidak diisi' }}</td>
                                <td>{{ str_replace('_', ' ', ucfirst($submission->status->value)) }}</td>
                                <td>{{ $submission->submitted_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('reviewer.submissions.show', $submission) }}">
                                        Buka detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav class="pagination" aria-label="Navigasi submission">
                {{ $submissions->links() }}
            </nav>
        @endif
    </section>
@endsection
