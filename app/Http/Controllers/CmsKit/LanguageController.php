<?php

namespace App\Http\Controllers\CmsKit;

use App\Models\CmsKit\Language;
use CMS\SiteManager\Support\ValidatesImageDimensions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Support\MediaStorage;
use App\Support\LocaleJsonManager;
use Illuminate\Support\Arr;

class LanguageController extends Controller
{
    use ValidatesImageDimensions;

    private LocaleJsonManager $localeManager;

    public function __construct(LocaleJsonManager $localeManager)
    {
        $this->localeManager = $localeManager;
    }

    protected function isEnglishLanguage(Language $language): bool
    {
        return strtolower((string) $language->code) === 'en';
    }

    protected function isEnglishOnlyAltKey(string $key): bool
    {
        $normalized = strtolower(trim($key));
        if ($normalized === '') {
            return false;
        }

        // We treat image alt translation keys as English-only (e.g. imageAlt, logoAlt, *.alt).
        return str_ends_with($normalized, 'alt')
            || str_contains($normalized, '.alt.')
            || str_ends_with($normalized, '.alt');
    }

    protected function flagImageRules(): array
    {
        $maxKb = config('cms-kit.images.languages.flag.max_size', 256);

        return [
            'flag_image' => 'nullable|image|max:' . $maxKb,
            'flag_alt' => 'nullable|string|max:255',
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $languages = Language::all();
            return \Yajra\DataTables\Facades\DataTables::of($languages)
                ->addColumn('flag_thumb', function ($row) {
                    if ($row->flag_image) {
                        return '<img src="' . e(media_url($row->flag_image)) . '" alt="' . e($row->flag_alt ?? '') . '" class="rounded border" style="height: 28px; width: auto;">';
                    }

                    return '<span class="text-muted">—</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $badgeClass = $row->status ? 'bg-success' : 'bg-secondary';
                    $btnClass = $row->status ? 'text-success' : 'text-secondary';
                    return '<form action="' . route('cms.languages.toggle-status', $row->id) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-link ' . $btnClass . ' p-0"><i class="fas ' . ($row->status ? 'fa-check-circle' : 'fa-times-circle') . '"></i></button>
                            </form>';
                })
                ->addColumn('default_badge', function ($row) {
                    if ($this->isEnglishLanguage($row) || $row->is_default) {
                        return '<span class="badge bg-primary">Default</span>';
                    }

                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('actions', function ($row) {
                    $flagUrl = $row->flag_image ? (media_url($row->flag_image) ?? '') : '';
                    $editBtn = '<button class="btn btn-sm btn-light border me-1 edit-language" data-id="' . $row->id . '" data-name="' . e($row->name) . '" data-code="' . e($row->code) . '" data-flag-url="' . e($flagUrl) . '" data-flag-alt="' . e($row->flag_alt ?? '') . '"><i class="fas fa-edit text-primary"></i></button>';
                    $deleteBtn = '';
                    if (!$row->is_default && !$this->isEnglishLanguage($row)) {
                        $deleteBtn = '<form action="' . route('cms.languages.destroy', $row->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Delete this language?\')">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-sm btn-light border text-danger"><i class="fas fa-trash"></i></button></form>';
                    }
                    return '<span class="d-inline-flex align-items-center flex-nowrap language-action-buttons">' . $editBtn . $deleteBtn . '</span>';
                })
                ->rawColumns(['flag_thumb', 'status_badge', 'default_badge', 'actions'])
                ->make(true);
        }
        return view('cms-kit::languages.index');
    }

    public function store(Request $request)
    {
        $flagConfig = config('cms-kit.images.languages.flag', []);
        $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s]+$/u'],
            'code' => ['required', 'string', 'max:10', 'unique:languages', 'regex:/^[\p{L}]+$/u'],
        ], $this->flagImageRules()), [
            'name.required' => 'Language name is required.',
            'name.regex' => 'Language name must contain only letters and spaces.',
            'code.required' => 'Language code is required.',
            'code.unique' => 'This language code already exists.',
            'code.regex' => 'Language code must contain only letters (no numbers or symbols).',
        ]);

        if ($request->hasFile('flag_image')) {
            $this->validateImageWithinLimits($request, 'flag_image', $flagConfig, 'Language flag');
        }

        $data = $request->only(['name', 'code', 'flag_alt']);
        if ($request->hasFile('flag_image')) {
            $data['flag_image'] = MediaStorage::store($request->file('flag_image'), 'languages/flags');
        }

        Language::create($data);
        return redirect()->back()->with('success', 'Language added.');
    }

    public function translationsEdit($id)
    {
        $language = Language::query()->findOrFail($id);
        $languageCode = $this->localeManager->normalizeCode((string) $language->code);
        $masterCode = $this->localeManager->masterCode();

        $masterData = $this->localeManager->readMasterLocale();
        if ($masterData === [] && $languageCode === $masterCode) {
            $this->localeManager->writeLocale($masterCode, []);
            $masterData = [];
        }

        if ($languageCode !== $masterCode) {
            $localeData = $this->localeManager->ensureLocaleForLanguage($languageCode);
        } else {
            $localeData = $masterData;
        }

        $masterLeafValues = $this->flattenLocaleLeafValues($masterData);
        $localeLeafValues = $this->flattenLocaleLeafValues($localeData);

        $rows = [];
        foreach ($masterLeafValues as $key => $defaultValue) {
            $currentValue = array_key_exists($key, $localeLeafValues) ? $localeLeafValues[$key] : $defaultValue;
            $isEnglishOnly = $languageCode !== $masterCode && $this->isEnglishOnlyAltKey($key);
            $rows[] = [
                'key' => $key,
                'value' => is_scalar($currentValue) || $currentValue === null ? (string) ($currentValue ?? '') : json_encode($currentValue),
                'default' => is_scalar($defaultValue) || $defaultValue === null ? (string) ($defaultValue ?? '') : json_encode($defaultValue),
                'is_english_only' => $isEnglishOnly,
            ];
        }

        return view('cms-kit::languages.translations', [
            'language' => $language,
            'rows' => $rows,
            'isDefaultLanguage' => $languageCode === $masterCode,
            'masterCode' => $masterCode,
        ]);
    }

    public function translationsUpdate(Request $request, $id)
    {
        $language = Language::query()->findOrFail($id);
        $languageCode = $this->localeManager->normalizeCode((string) $language->code);
        $masterCode = $this->localeManager->masterCode();

        $validated = $request->validate([
            'translations' => ['required', 'array'],
        ]);

        $submitted = $validated['translations'] ?? [];
        $masterData = $this->localeManager->readMasterLocale();

        $masterLeafValues = $this->flattenLocaleLeafValues($masterData);
        $resolvedLeafValues = [];

        foreach ($masterLeafValues as $key => $defaultValue) {
            $submittedValue = array_key_exists($key, $submitted) ? $submitted[$key] : $defaultValue;
            if ($languageCode !== $masterCode && $this->isEnglishOnlyAltKey($key)) {
                $submittedValue = $defaultValue;
            }
            $resolvedLeafValues[$key] = $this->castToMasterType($submittedValue, $defaultValue);
        }

        $updatedPayload = $this->buildLocalePayloadFromLeafValues($masterData, $resolvedLeafValues);
        $this->localeManager->writeLocale($languageCode, $updatedPayload);

        if ($languageCode === $masterCode) {
            $codes = Language::query()->pluck('code')->all();
            $this->localeManager->synchronizeLanguageCodes($codes);
        }

        return redirect()
            ->route('cms.languages.translations.edit', ['id' => $language->id])
            ->with('success', 'Language translations updated.');
    }

    public function update(Request $request, $id)
    {
        $language = Language::findOrFail($id);
        $isEnglish = $this->isEnglishLanguage($language);

        $flagConfig = config('cms-kit.images.languages.flag', []);
        $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s]+$/u'],
            'code' => $isEnglish
                ? ['required', 'string', 'in:en']
                : ['required', 'string', 'max:10', 'unique:languages,code,' . $id, 'regex:/^[\p{L}]+$/u'],
        ], $this->flagImageRules()), [
            'name.required' => 'Language name is required.',
            'name.regex' => 'Language name must contain only letters and spaces.',
            'code.required' => 'Language code is required.',
            'code.unique' => 'This language code already exists.',
            'code.regex' => 'Language code must contain only letters (no numbers or symbols).',
            'code.in' => 'English language code must remain en.',
        ]);

        if ($request->hasFile('flag_image')) {
            $this->validateImageWithinLimits($request, 'flag_image', $flagConfig, 'Language flag');
        }

        $data = $request->only(['name', 'code', 'flag_alt']);
        if ($request->hasFile('flag_image')) {
            if ($language->flag_image) {
                MediaStorage::delete($language->flag_image);
            }
            $data['flag_image'] = MediaStorage::store($request->file('flag_image'), 'languages/flags');
        }

        $language->update($data);
        if ($isEnglish && (!$language->is_default || !$language->status)) {
            $language->update([
                'is_default' => true,
                'status' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Language updated.');
    }

    public function toggleStatus($id)
    {
        $language = Language::findOrFail($id);

        if (($language->is_default || $this->isEnglishLanguage($language)) && $language->status) {
            return redirect()->back()->with('error', 'Cannot deactivate the default language.');
        }

        $language->update(['status' => !$language->status]);
        return redirect()->back()->with('success', 'Status updated.');
    }

    public function setDefault($id)
    {
        $language = Language::findOrFail($id);

        if (!$this->isEnglishLanguage($language)) {
            return redirect()->back()->with('error', 'English must remain the default language.');
        }

        Language::query()->update(['is_default' => false]);
        $language->update([
            'is_default' => true,
            'status' => true,
        ]);

        return redirect()->back()->with('success', 'English remains the default language.');
    }

    public function destroy($id)
    {
        $language = Language::findOrFail($id);
        if ($language->is_default || $this->isEnglishLanguage($language)) {
            return redirect()->back()->with('error', 'Cannot delete default language.');
        }
        if ($language->flag_image) {
            MediaStorage::delete($language->flag_image);
        }
        $language->delete();
        return redirect()->back()->with('success', 'Language deleted.');
    }

    private function flattenLocaleLeafValues(array $data, string $prefix = ''): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $result += $this->flattenLocaleLeafValues($value, $path);
                continue;
            }

            $result[$path] = $value;
        }

        return $result;
    }

    private function buildLocalePayloadFromLeafValues(array $masterData, array $leafValues): array
    {
        $masterLeafValues = $this->flattenLocaleLeafValues($masterData);
        $payload = [];

        foreach ($masterLeafValues as $key => $defaultValue) {
            Arr::set($payload, $key, array_key_exists($key, $leafValues) ? $leafValues[$key] : $defaultValue);
        }

        return $payload;
    }

    private function castToMasterType(mixed $value, mixed $masterValue): mixed
    {
        if (is_bool($masterValue)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }

        if (is_int($masterValue)) {
            return (int) $value;
        }

        if (is_float($masterValue)) {
            return (float) $value;
        }

        if (is_null($masterValue)) {
            return $value === '' ? null : $value;
        }

        if (is_string($masterValue)) {
            return (string) $value;
        }

        return $value;
    }
}
