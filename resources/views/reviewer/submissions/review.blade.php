<h1>Review Scope Version {{ $version->version_number }}</h1>

<pre>{{ json_encode($version->snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

<form method="POST" action="{{ route('reviewer.submissions.versions.approve', [
    'submission' => $submission->id,
    'version' => $version->id,
]) }}">
    @csrf
    <label for="approve-note">Catatan persetujuan</label>
    <textarea id="approve-note" name="note" required minlength="10" maxlength="5000">{{ old('note') }}</textarea>
    @error('note') <p role="alert">{{ $message }}</p> @enderror
    <button type="submit">Setujui scope</button>
</form>

<form method="POST" action="{{ route('reviewer.submissions.versions.reject', [
    'submission' => $submission->id,
    'version' => $version->id,
]) }}">
    @csrf
    <label for="reject-note">Alasan penolakan</label>
    <textarea id="reject-note" name="note" required minlength="10" maxlength="5000">{{ old('note') }}</textarea>
    @error('note') <p role="alert">{{ $message }}</p> @enderror
    <button type="submit">Tolak scope</button>
</form>
