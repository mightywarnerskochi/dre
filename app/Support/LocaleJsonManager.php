<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class LocaleJsonManager
{
    private const MASTER_CODE = 'en';

    public function masterCode(): string
    {
        return self::MASTER_CODE;
    }

    public function normalizeCode(string $code): string
    {
        return strtolower(trim($code));
    }

    public function localesDirectory(): string
    {
        return resource_path('js/locales');
    }

    public function localeFilePath(string $code): string
    {
        return $this->localesDirectory().DIRECTORY_SEPARATOR.$this->normalizeCode($code).'.json';
    }

    public function ensureLocalesDirectory(): void
    {
        $dir = $this->localesDirectory();
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
    }

    public function readMasterLocale(): array
    {
        return $this->readLocale($this->masterCode());
    }

    public function readLocale(string $code): array
    {
        $path = $this->localeFilePath($code);
        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode((string) File::get($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    public function writeLocale(string $code, array $data): void
    {
        $this->ensureLocalesDirectory();

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            $json = '{}';
        }

        File::put($this->localeFilePath($code), $json.PHP_EOL);
    }

    public function deleteLocaleFile(string $code): void
    {
        $normalizedCode = $this->normalizeCode($code);
        if ($normalizedCode === $this->masterCode()) {
            return;
        }

        $path = $this->localeFilePath($normalizedCode);
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    public function synchronizeLocaleDataWithMaster(array $candidate, ?array $master = null): array
    {
        $master = $master ?? $this->readMasterLocale();

        if ($master === []) {
            return $candidate;
        }

        return $this->syncNode($master, $candidate);
    }

    public function ensureLocaleForLanguage(string $code): array
    {
        $normalizedCode = $this->normalizeCode($code);
        $master = $this->readMasterLocale();

        if ($normalizedCode === $this->masterCode()) {
            if ($master === []) {
                $this->writeLocale($normalizedCode, []);
                return [];
            }

            $syncedMaster = $this->synchronizeLocaleDataWithMaster($master, $master);
            $this->writeLocale($normalizedCode, $syncedMaster);
            return $syncedMaster;
        }

        if ($master === []) {
            $this->writeLocale($normalizedCode, []);
            return [];
        }

        $current = $this->readLocale($normalizedCode);
        $synced = $this->synchronizeLocaleDataWithMaster($current, $master);
        $synced = $this->enforceEnglishOnlyAltLeafValues($master, $synced);
        $this->writeLocale($normalizedCode, $synced);

        return $synced;
    }

    public function synchronizeLanguageCodes(iterable $codes): void
    {
        $master = $this->readMasterLocale();
        if ($master === []) {
            return;
        }

        foreach ($codes as $code) {
            $normalized = $this->normalizeCode((string) $code);
            if ($normalized === '' || $normalized === $this->masterCode()) {
                continue;
            }

            $current = $this->readLocale($normalized);
            $synced = $this->synchronizeLocaleDataWithMaster($current, $master);
            $synced = $this->enforceEnglishOnlyAltLeafValues($master, $synced);
            $this->writeLocale($normalized, $synced);
        }
    }

    public function allLocaleMessages(): array
    {
        $this->ensureLocalesDirectory();
        $masterCode = $this->masterCode();
        $master = $this->readMasterLocale();
        if ($master === [] && ! File::exists($this->localeFilePath($masterCode))) {
            $this->writeLocale($masterCode, []);
            $master = [];
        }

        $files = File::files($this->localesDirectory());

        $messages = [];
        foreach ($files as $file) {
            if (strtolower($file->getExtension()) !== 'json') {
                continue;
            }

            $code = strtolower($file->getBasename('.json'));
            $decoded = json_decode((string) File::get($file->getPathname()), true);
            if (is_array($decoded)) {
                if ($code !== $masterCode && $master !== []) {
                    $decoded = $this->synchronizeLocaleDataWithMaster($decoded, $master);
                    $decoded = $this->enforceEnglishOnlyAltLeafValues($master, $decoded);
                    $this->writeLocale($code, $decoded);
                }

                $messages[$code] = $decoded;
            }
        }

        if (! isset($messages[$masterCode])) {
            $messages[$masterCode] = $master;
        }

        ksort($messages);

        return $messages;
    }

    private function syncNode(mixed $masterNode, mixed $candidateNode): mixed
    {
        if (! is_array($masterNode)) {
            if (is_array($candidateNode)) {
                return $masterNode;
            }

            return $candidateNode ?? $masterNode;
        }

        $candidateNode = is_array($candidateNode) ? $candidateNode : [];

        $masterIsAssoc = Arr::isAssoc($masterNode);
        $result = [];

        foreach ($masterNode as $key => $masterValue) {
            $candidateValue = array_key_exists($key, $candidateNode) ? $candidateNode[$key] : null;
            $result[$key] = $this->syncNode($masterValue, $candidateValue);
        }

        if (! $masterIsAssoc) {
            return array_values($result);
        }

        return $result;
    }

    private function enforceEnglishOnlyAltLeafValues(array $master, array $candidate): array
    {
        return $this->enforceEnglishOnlyAltNode($master, $candidate);
    }

    private function enforceEnglishOnlyAltNode(mixed $masterNode, mixed $candidateNode, string $path = ''): mixed
    {
        if (! is_array($masterNode)) {
            if ($this->isEnglishOnlyAltKeyPath($path)) {
                return $masterNode;
            }

            return $candidateNode;
        }

        $candidateNode = is_array($candidateNode) ? $candidateNode : [];
        $result = [];
        $masterIsAssoc = Arr::isAssoc($masterNode);

        foreach ($masterNode as $key => $masterValue) {
            $childPath = $path === '' ? (string) $key : $path.'.'.$key;
            $candidateValue = array_key_exists($key, $candidateNode) ? $candidateNode[$key] : null;
            $result[$key] = $this->enforceEnglishOnlyAltNode($masterValue, $candidateValue, $childPath);
        }

        if (! $masterIsAssoc) {
            return array_values($result);
        }

        return $result;
    }

    private function isEnglishOnlyAltKeyPath(string $path): bool
    {
        $normalized = strtolower(trim($path));
        if ($normalized === '') {
            return false;
        }

        return str_ends_with($normalized, 'alt')
            || str_contains($normalized, '.alt.')
            || str_ends_with($normalized, '.alt');
    }
}
