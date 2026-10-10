@extends('layouts.app')

@section('content')
    <section class="form-panel" aria-labelledby="intake-title">
        <p class="eyebrow">Formulir calon klien</p>
        <h1 id="intake-title">Ceritakan kebutuhan proyek</h1>
        <p class="lede">
            Isi informasi yang sudah kamu ketahui. Reviewer akan memeriksa kiriman ini
            sebelum membuat scope review.
        </p>

        @if ($errors->any())
            <div class="error-panel" role="alert" aria-labelledby="error-title">
                <h2 id="error-title">Periksa kembali isian berikut</h2>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('intake.store') }}" class="intake-form">
            @csrf

            <div class="field-group">
                <label for="contact_name">Nama kontak</label>
                <input
                    id="contact_name"
                    name="contact_name"
                    type="text"
                    value="{{ old('contact_name') }}"
                    autocomplete="name"
                    required
                >
                @error('contact_name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field-group">
                <label for="contact_email">Email kontak</label>
                <input
                    id="contact_email"
                    name="contact_email"
                    type="email"
                    value="{{ old('contact_email') }}"
                    autocomplete="email"
                    required
                >
                @error('contact_email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field-group">
                <label for="organization_name">Nama organisasi <span>(opsional)</span></label>
                <input
                    id="organization_name"
                    name="organization_name"
                    type="text"
                    value="{{ old('organization_name') }}"
                    autocomplete="organization"
                >
                @error('organization_name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field-group">
                <label for="problem_statement">Masalah yang ingin diselesaikan</label>
                <textarea
                    id="problem_statement"
                    name="problem_statement"
                    rows="6"
                    required
                >{{ old('problem_statement') }}</textarea>
                <p class="field-help">Jelaskan keadaan yang ingin diperbaiki.</p>
                @error('problem_statement')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field-group">
                <label for="desired_outcome">Hasil yang diharapkan</label>
                <textarea
                    id="desired_outcome"
                    name="desired_outcome"
                    rows="6"
                    required
                >{{ old('desired_outcome') }}</textarea>
                <p class="field-help">Jelaskan perubahan yang ingin dicapai.</p>
                @error('desired_outcome')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field-group">
                <label for="constraints">Batasan proyek <span>(opsional)</span></label>
                <textarea
                    id="constraints"
                    name="constraints"
                    rows="4"
                >{{ old('constraints') }}</textarea>
                @error('constraints')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field-group">
                <label for="additional_context">Konteks tambahan <span>(opsional)</span></label>
                <textarea
                    id="additional_context"
                    name="additional_context"
                    rows="4"
                >{{ old('additional_context') }}</textarea>
                @error('additional_context')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <button class="primary-button" type="submit">Kirim intake untuk ditinjau</button>
        </form>
    </section>
@endsection
