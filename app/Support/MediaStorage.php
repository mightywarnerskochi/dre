<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class MediaStorage
{
    protected static function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));

        // If an absolute local path is accidentally passed, trim it to the storage-relative path.
        foreach (['/storage/app/public/', '/public/storage/'] as $marker) {
            $position = strpos($path, $marker);
            if ($position !== false) {
                $path = substr($path, $position + strlen($marker));
                break;
            }
        }

        if (preg_match('/^[A-Za-z]:\//', $path) === 1) {
            $path = preg_replace('/^[A-Za-z]:\//', '', $path) ?? $path;
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = ltrim(substr($path, strlen('storage/')), '/');
        }

        return $path;
    }

    public static function diskName(): string
    {
        return (string) config('cms-kit.storage.disk', 'public');
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(self::diskName());
    }

    /**
     * Public URL for a stored path, or absolute URL passthrough.
     */
    public static function url(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $path = trim($path);
        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            $path = ltrim(substr($path, strlen('/storage/')), '/');
        }

        $path = ltrim(trim($path), '/');

        try {
            return self::disk()->url($path);
        } catch (\Throwable $e) {
            // Log for debugging if necessary, but returning null prevents crashing the whole page.
            return null;
        }
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store(trim($directory, '/'), self::diskName());
    }

    public static function storeAs(UploadedFile $file, string $directory, string $name): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = $name.($extension ? '.'.$extension : '');

        return $file->storeAs(trim($directory, '/'), $filename, self::diskName());
    }

    public static function put(string $path, $contents, array $options = []): bool
    {
        $path = self::normalizePath($path);
        if ($path === '') {
            return false;
        }

        $merged = array_merge(['visibility' => 'public'], $options);

        if (self::diskName() === 'cloudinary') {
            $tempFile = null;
            $createTempImageFile = static function (): ?string {
                $base = tempnam(sys_get_temp_dir(), 'cms-media-');
                if ($base === false) {
                    return null;
                }

                $withExtension = $base.'.jpg';
                @rename($base, $withExtension);

                return $withExtension;
            };

            if (is_resource($contents)) {
                $streamData = stream_get_contents($contents);
                if ($streamData === false) {
                    return false;
                }
                $tempFile = $createTempImageFile();
                if ($tempFile === null) {
                    return false;
                }
                file_put_contents($tempFile, $streamData);
                $contents = $tempFile;
            } elseif (is_string($contents) && ! is_file($contents)) {
                $tempFile = $createTempImageFile();
                if ($tempFile === null) {
                    return false;
                }
                file_put_contents($tempFile, $contents);
                $contents = $tempFile;
            }

            try {
                return self::disk()->put($path, $contents, $merged);
            } finally {
                if ($tempFile && is_file($tempFile)) {
                    @unlink($tempFile);
                }
            }
        }

        return self::disk()->put($path, $contents, $merged);
    }

    public static function delete(?string $path): void
    {
        if (! is_string($path) || trim($path) === '') {
            return;
        }

        $path = self::normalizePath($path);

        if ($path === '' || ! self::disk()->exists($path)) {
            return;
        }

        self::disk()->delete($path);
    }

    /**
     * @param  iterable<string|null>  $paths
     */
    public static function deleteMany(iterable $paths): void
    {
        foreach ($paths as $path) {
            self::delete($path);
        }
    }

    public static function deleteDirectory(string $directory): void
    {
        $directory = trim($directory, '/');
        if ($directory === '') {
            return;
        }

        self::disk()->deleteDirectory($directory);
    }

    public static function exists(string $path): bool
    {
        $path = self::normalizePath($path);

        if ($path === '') {
            return false;
        }

        return self::disk()->exists($path);
    }

    public static function makeDirectory(string $path): bool
    {
        $path = self::normalizePath($path);

        return self::disk()->makeDirectory($path);
    }

    public static function move(string $from, string $to): bool
    {
        $from = self::normalizePath($from);
        $to = self::normalizePath($to);

        return self::disk()->move($from, $to);
    }
}
