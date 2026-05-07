<?php

namespace App\Observers;

use App\Models\CmsKit\Language;
use App\Support\LocaleJsonManager;

class LanguageObserver
{
    public function __construct(
        private readonly LocaleJsonManager $localeManager,
    ) {}

    public function saving(Language $language): void
    {
        $language->code = $this->localeManager->normalizeCode((string) $language->code);
        $language->name = trim((string) $language->name);
    }

    public function created(Language $language): void
    {
        $this->localeManager->ensureLocaleForLanguage((string) $language->code);
    }

    public function updated(Language $language): void
    {
        $oldCode = $this->localeManager->normalizeCode((string) $language->getOriginal('code'));
        $newCode = $this->localeManager->normalizeCode((string) $language->code);
        $masterCode = $this->localeManager->masterCode();

        if ($oldCode !== $newCode) {
            $oldPayload = $this->localeManager->readLocale($oldCode);
            $this->localeManager->deleteLocaleFile($oldCode);

            if ($newCode !== $masterCode) {
                $currentPayload = $this->localeManager->readLocale($newCode);
                $candidatePayload = $currentPayload !== [] ? $currentPayload : $oldPayload;
                $syncedPayload = $this->localeManager->synchronizeLocaleDataWithMaster($candidatePayload);
                $this->localeManager->writeLocale($newCode, $syncedPayload);
            }
        }

        $codes = Language::query()->pluck('code')->all();
        $this->localeManager->synchronizeLanguageCodes($codes);
    }

    public function deleted(Language $language): void
    {
        $this->localeManager->deleteLocaleFile((string) $language->code);
    }
}
