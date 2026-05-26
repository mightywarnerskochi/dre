<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SitemapCrawlLinks
{
    public function paths(): array
    {
        return collect(config('cms.sitemap.static_paths', []))
            ->merge($this->dynamicPaths())
            ->map(fn ($path) => $this->normalizePath($path))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function urls(): array
    {
        return collect($this->paths())
            ->map(fn ($path) => url($path))
            ->values()
            ->all();
    }

    private function dynamicPaths(): array
    {
        return collect(config('cms.sitemap.models', []))
            ->flatMap(function ($options, $key) {
                $modelClass = is_string($key) ? $key : $options;
                $options = is_array($options) ? $options : [];

                if (! is_string($modelClass) || ! class_exists($modelClass)) {
                    return [];
                }

                $model = new $modelClass;
                if (! $model instanceof Model || ! $this->tableExists($model)) {
                    return [];
                }

                return $this->pathsForModel($model, $options);
            })
            ->all();
    }

    private function pathsForModel(Model $model, array $options): array
    {
        $query = $model->newQuery();

        if ($this->columnExists($model, 'status')) {
            $query->where('status', true);
        }

        if (isset($options['url_prefix'])) {
            $slugField = (string) ($options['slug_field'] ?? 'slug');

            if (! $this->columnExists($model, $slugField)) {
                return [];
            }

            return $query
                ->whereNotNull($slugField)
                ->where($slugField, '!=', '')
                ->pluck($slugField)
                ->map(fn ($slug) => $this->joinPath($options['url_prefix'], $slug))
                ->all();
        }

        return $query
            ->get()
            ->map(fn (Model $record) => $this->pathFromRecord($record))
            ->filter()
            ->all();
    }

    private function pathFromRecord(Model $record): ?string
    {
        if (method_exists($record, 'getSitemapUrl')) {
            return $record->getSitemapUrl();
        }

        $url = $record->getAttribute('url');

        return is_string($url) && $url !== '' ? $url : null;
    }

    private function tableExists(Model $model): bool
    {
        return Schema::connection($model->getConnectionName())->hasTable($model->getTable());
    }

    private function columnExists(Model $model, string $column): bool
    {
        return Schema::connection($model->getConnectionName())->hasColumn($model->getTable(), $column);
    }

    private function joinPath(string $prefix, mixed $value): string
    {
        return rtrim($prefix, '/').'/'.ltrim((string) $value, '/');
    }

    private function normalizePath(mixed $path): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $path = parse_url($path, PHP_URL_PATH) ?: '/';
        }

        return '/'.ltrim($path, '/');
    }
}
