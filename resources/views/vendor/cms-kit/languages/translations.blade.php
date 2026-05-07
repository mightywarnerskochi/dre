@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.languages.index') }}">Languages</a></li>
    <li class="breadcrumb-item active" aria-current="page">Static Text ({{ strtoupper($language->code) }})</li>
@endsection

@section('content')
@php
    $oldTranslations = old('translations', []);
@endphp
<div class="card mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="mb-1">Manage Static Text — {{ $language->name }} ({{ strtoupper($language->code) }})</h5>
            <p class="text-muted mb-0">
                @if($isDefaultLanguage)
                    This is the master language file. Updating this will synchronize key structure to other languages.
                @else
                    Missing keys are auto-filled from English. ALT-text keys stay English-only.
                @endif
            </p>
        </div>
        <a href="{{ route('cms.languages.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Languages
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <form action="{{ route('cms.languages.translations.update', ['id' => $language->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-6">
                    <label for="translationSearch" class="form-label small fw-bold">Search keys</label>
                    <input
                        type="text"
                        id="translationSearch"
                        class="form-control form-control-sm"
                        placeholder="Type key name (e.g. listing.heroTitle)"
                    >
                </div>
                <div class="col-md-6 text-md-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i> Save Translations
                    </button>
                </div>
            </div>

            @if(empty($rows))
                <div class="alert alert-warning mb-0">
                    No translation keys found in the master language file (<code>{{ $masterCode }}.json</code>).
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="translationsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 28%;">Key</th>
                                <th style="width: 36%;">Value ({{ strtoupper($language->code) }})</th>
                                <th style="width: 36%;">English Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                @php
                                    $isEnglishOnly = (bool) ($row['is_english_only'] ?? false);
                                    $rowValue = $oldTranslations[$row['key']] ?? $row['value'];
                                    if ($isEnglishOnly) {
                                        $rowValue = $row['default'];
                                    }
                                @endphp
                                <tr class="translation-row">
                                    <td>
                                        <code class="small">{{ $row['key'] }}</code>
                                        @if($isEnglishOnly)
                                            <div><span class="badge bg-light text-dark border mt-1">English only</span></div>
                                        @endif
                                    </td>
                                    <td>
                                        <textarea
                                            name="translations[{{ $row['key'] }}]"
                                            class="form-control form-control-sm {{ $isEnglishOnly ? 'bg-light' : '' }}"
                                            rows="2"
                                            {{ $isEnglishOnly ? 'readonly' : '' }}
                                        >{{ $rowValue }}</textarea>
                                    </td>
                                    <td>
                                        <div class="small text-muted" style="white-space: pre-wrap;">{{ $row['default'] }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('translationSearch');
        const rows = Array.from(document.querySelectorAll('#translationsTable .translation-row'));

        if (!searchInput || !rows.length) {
            return;
        }

        searchInput.addEventListener('input', function () {
            const term = String(searchInput.value || '').toLowerCase().trim();

            rows.forEach((row) => {
                const keyEl = row.querySelector('code');
                const key = String(keyEl?.textContent || '').toLowerCase();
                const show = term === '' || key.includes(term);
                row.style.display = show ? '' : 'none';
            });
        });
    });
</script>
@endpush
