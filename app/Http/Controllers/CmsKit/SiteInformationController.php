<?php

namespace App\Http\Controllers\CmsKit;

use App\Models\CmsKit\SiteInformation;
use App\Models\CmsKit\Language;
use App\Support\MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SiteInformationController extends Controller
{
    protected array $translatableFields = [
        'company_name',
        'address',
        'country',
        'po_box',
        'fax',
        'privacy_policy',
        'terms_and_conditions',
        'disclaimer',
        'footer_description',
    ];

    protected function getValidationRules(bool $hasExistingRecord = false): array
    {
        $siteInfoConfig = config('cms-kit.database.site-information', []);
        $requiredFields = array_unique(array_merge(
            $siteInfoConfig['required'] ?? [],
            ['receipt_email', 'logo', 'favicon']
        ));
        $languages = Language::active()->get();
        $rules = [
            'extra_fields' => 'nullable|array',
            'translations' => 'nullable|array',
        ];

        foreach ([
            'phone_1', 'phone_2', 'phone_3', 'phone_4',
            'whatsapp_number',
            'logo_alt', 'footer_logo_alt',
            'gtag'
        ] as $field) {
            if ($siteInfoConfig[$field] ?? true) {
                $prefix = in_array($field, $requiredFields) ? 'required' : 'nullable';
                $rules[$field] = $prefix . '|string|max:255';
            }
        }

        foreach (['email_1', 'email_2', 'email_3', 'email_4', 'receipt_email'] as $field) {
            if ($siteInfoConfig[$field] ?? true) {
                $prefix = in_array($field, $requiredFields) ? 'required' : 'nullable';
                $rules[$field] = [
                    $prefix,
                    'email:filter',
                    'max:255',
                    'regex:/^[^\\s@]+@([A-Za-z0-9-]+\\.)+[A-Za-z]{2,}$/',
                ];
            }
        }

        foreach (['facebook', 'twitter', 'linkedin', 'instagram', 'tiktok', 'snapchat', 'pinterest', 'youtube', 'skype', 'whatsapp_social', 'vimeo'] as $field) {
            if ($siteInfoConfig[$field] ?? true) {
                $prefix = in_array($field, $requiredFields) ? 'required' : 'nullable';
                $rules[$field] = $prefix . '|url|max:255';
            }
        }

        foreach (['custom_head_script', 'custom_body_script'] as $field) {
            if ($siteInfoConfig[$field] ?? true) {
                $rules[$field] = in_array($field, $requiredFields) ? 'required|string' : 'nullable|string';
            }
        }

        foreach (['logo' => 2048, 'favicon' => 1024, 'footer_logo' => 2048] as $field => $maxSize) {
            if ($siteInfoConfig[$field] ?? true) {
                $existingRecord = SiteInformation::first();
                $needsFile = in_array($field, $requiredFields)
                    && (
                        !$hasExistingRecord
                        || !$existingRecord?->{$field}
                        || request()->boolean("remove_{$field}")
                    );
                $rules[$field] = ($needsFile ? 'required' : 'nullable') . '|image|max:' . $maxSize;
                $rules["remove_{$field}"] = 'nullable|boolean';
            }
        }

        foreach (($siteInfoConfig['extra_fields'] ?? []) as $fieldName => $fieldConfig) {
            if (($fieldConfig['type'] ?? null) !== 'file') {
                continue;
            }

            $mimes = collect($fieldConfig['mimes'] ?? [])
                ->filter(fn ($mime) => is_string($mime) && $mime !== '')
                ->implode(',');
            $maxSize = (int) ($fieldConfig['max_size'] ?? 2048);

            $rules["extra_fields.{$fieldName}"] = 'nullable|file'
                . ($mimes !== '' ? "|mimes:{$mimes}" : '')
                . "|max:{$maxSize}";
            $rules["remove_{$fieldName}"] = 'nullable|boolean';
        }

        foreach ($languages as $lang) {
            foreach ($this->translatableFields as $field) {
                if ($siteInfoConfig[$field] ?? true) {
                    $prefix = in_array($field, $requiredFields) ? 'required' : 'nullable';
                    $rules["translations.{$lang->code}.{$field}"] = in_array($field, ['privacy_policy', 'terms_and_conditions', 'disclaimer']) ? $prefix . '|string' : $prefix . '|string|max:255';
                    if (in_array($field, ['address', 'footer_description'])) {
                        $rules["translations.{$lang->code}.{$field}"] = $prefix . '|string';
                    }
                }
            }

        }

        return $rules;
    }

    protected function getValidationAttributes(): array
    {
        return [
            'email_1' => 'email 1',
            'email_2' => 'email 2',
            'email_3' => 'email 3',
            'email_4' => 'email 4',
            'receipt_email' => 'recipient email',
            'logo' => 'main logo',
            'favicon' => 'favicon',
        ];
    }

    protected function mergeTranslatableExtraFields(array $translations): array
    {
        $fieldConfig = config('cms-kit.database.site-information.extra_fields', []);
        $translatableFields = collect($fieldConfig)->filter(fn ($field) => $field['translatable'] ?? false)->keys();

        foreach ($translations as $lang => $values) {
            $translations[$lang]['extra_fields'] = [];
            foreach ($translatableFields as $fieldName) {
                $translations[$lang]['extra_fields'][$fieldName] = data_get($values, "extra_fields.{$fieldName}");
            }
        }

        return $translations;
    }

    protected function getDefaultLanguageCode()
    {
        $defaultLanguage = Language::active()->where('is_default', true)->first();

        return $defaultLanguage?->code
            ?? Language::active()->orderByDesc('is_default')->value('code')
            ?? config('app.fallback_locale');
    }

    public function index()
    {
        $siteInfo = SiteInformation::first() ?? new SiteInformation();
        $languages = Language::active()->get();
        return view('cms-kit::site-information.index', compact('siteInfo', 'languages'));
    }

    public function update(Request $request)
    {
        $siteInfo = SiteInformation::first() ?? new SiteInformation();
        $defaultLanguageCode = $this->getDefaultLanguageCode();

        $data = $request->validate(
            $this->getValidationRules($siteInfo->exists),
            [],
            $this->getValidationAttributes()
        );
        // Handle File Uploads
        if ($request->hasFile('logo')) {
            if ($siteInfo->logo) {
                MediaStorage::delete($siteInfo->logo);
            }
            $data['logo'] = MediaStorage::storeAs($request->file('logo'), 'site-info', 'logo');
        } elseif ($request->boolean('remove_logo') && $siteInfo->logo) {
            MediaStorage::delete($siteInfo->logo);
            $data['logo'] = null;
            $data['logo_alt'] = null;
        }
        
        if ($request->hasFile('favicon')) {
            if ($siteInfo->favicon) {
                MediaStorage::delete($siteInfo->favicon);
            }
            $data['favicon'] = MediaStorage::storeAs($request->file('favicon'), 'site-info', 'fav');
        } elseif ($request->boolean('remove_favicon') && $siteInfo->favicon) {
            MediaStorage::delete($siteInfo->favicon);
            $data['favicon'] = null;
        }

        if ($request->hasFile('footer_logo')) {
            if ($siteInfo->footer_logo) {
                MediaStorage::delete($siteInfo->footer_logo);
            }
            $data['footer_logo'] = MediaStorage::storeAs($request->file('footer_logo'), 'site-info', 'footer_logo');
        } elseif ($request->boolean('remove_footer_logo') && $siteInfo->footer_logo) {
            MediaStorage::delete($siteInfo->footer_logo);
            $data['footer_logo'] = null;
            $data['footer_logo_alt'] = null;
        }

        $existingExtraFields = is_array($siteInfo->extra_fields) ? $siteInfo->extra_fields : [];
        $extraFields = $existingExtraFields;
        foreach (config('cms-kit.database.site-information.extra_fields', []) as $key => $field) {
            $isFileField = ($field['type'] ?? null) === 'file';
            if ($isFileField) {
                $existingPath = data_get($existingExtraFields, $key);
                $removeRequested = $request->boolean("remove_{$key}");

                if ($request->hasFile("extra_fields.{$key}")) {
                    if (is_string($existingPath) && $existingPath !== '') {
                        MediaStorage::delete($existingPath);
                    }

                    $directory = trim((string) ($field['directory'] ?? 'site-info'), '/');
                    $storageName = trim((string) ($field['storage_name'] ?? ''), '/');
                    $uploadedFile = $request->file("extra_fields.{$key}");

                    $extraFields[$key] = $storageName !== ''
                        ? MediaStorage::storeAs($uploadedFile, $directory, $storageName)
                        : MediaStorage::store($uploadedFile, $directory);
                } elseif ($removeRequested) {
                    if (is_string($existingPath) && $existingPath !== '') {
                        MediaStorage::delete($existingPath);
                    }
                    $extraFields[$key] = null;
                } else {
                    $extraFields[$key] = $existingPath;
                }

                continue;
            }

            $extraFields[$key] = $request->input("extra_fields.{$key}");
        }
        $data['extra_fields'] = $extraFields;
        $translations = $this->mergeTranslatableExtraFields($request->input('translations', []));
        $data['translations'] = $translations;

        foreach ($this->translatableFields as $field) {
            $data[$field] = data_get($translations, "{$defaultLanguageCode}.{$field}", $request->input($field));
        }

        $siteInfo->fill($data);
        $siteInfo->save();

        return redirect()->route('cms.site-information.index')->with('success', 'Site Information updated successfully.');
    }
}
