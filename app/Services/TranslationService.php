<?php

namespace App\Services;

use App\Models\Translation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class TranslationService
{
    protected $cacheTimeout = 86400; // 24 hours
    protected $supportedLocales = ['en', 'es', 'fr', 'de', 'it'];

    public function getTranslation(string $key, array $replace = [], string $locale = null, string $group = 'messages'): string
    {
        $locale = $locale ?? app()->getLocale();
        $cacheKey = "translation.{$locale}.{$group}.{$key}";

        return Cache::tags(['translations'])->remember($cacheKey, $this->cacheTimeout, function () use ($key, $locale, $group, $replace) {
            // Try to get from database
            $translation = Translation::getTranslation($key, $locale, $group);

            // If not found in database, try to get from language files
            if (!$translation) {
                $translation = Lang::get("{$group}.{$key}", $replace, $locale);

                // If translation is found in files, store it in database for future use
                if ($translation !== "{$group}.{$key}") {
                    $this->setTranslation($key, [$locale => $translation], $group);
                }
            }

            // If still not found, try fallback locale
            if ($translation === "{$group}.{$key}") {
                $fallbackLocale = config('app.fallback_locale', 'en');
                $translation = Translation::getTranslation($key, $fallbackLocale, $group);

                if (!$translation) {
                    $translation = Lang::get("{$group}.{$key}", $replace, $fallbackLocale);
                }
            }

            // If still not found, return the key as a last resort
            if (!$translation || $translation === "{$group}.{$key}") {
                return $key;
            }

            return $this->replaceParameters($translation, $replace);
        });
    }

    public function setTranslation(string $key, array $translations, string $group = 'messages'): void
    {
        foreach ($translations as $locale => $value) {
            if (in_array($locale, $this->supportedLocales)) {
                Translation::setTranslation($key, $value, $locale, $group);
                Cache::tags(['translations'])->forget("translation.{$locale}.{$group}.{$key}");
            }
        }
    }

    public function importFromFiles(): void
    {
        foreach ($this->supportedLocales as $locale) {
            $path = lang_path($locale);
            if (File::exists($path)) {
                $files = File::files($path);
                foreach ($files as $file) {
                    $group = pathinfo($file, PATHINFO_FILENAME);
                    $translations = require $file->getPathname();

                    // Convert nested array to flat array with dot notation
                    $flatTranslations = [];
                    $this->flattenArray($translations, $flatTranslations);

                    // Process in chunks
                    foreach (array_chunk($flatTranslations, 50, true) as $chunk) {
                        foreach ($chunk as $key => $value) {
                            try {
                                Translation::setTranslation($key, $value, $locale, $group);
                            } catch (\Exception $e) {
                                Log::error("Error importing translation: {$e->getMessage()}", [
                                    'key' => $key,
                                    'locale' => $locale,
                                    'group' => $group
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    protected function flattenArray(array $array, array &$result, string $prefix = ''): void
    {
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $this->flattenArray($value, $result, $newKey);
            } else {
                $result[$newKey] = $value;
            }
        }
    }

    protected function replaceParameters(string $text, array $replace): string
    {
        if (empty($replace)) {
            return $text;
        }

        return collect($replace)->reduce(function ($text, $value, $key) {
            return str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                $value,
                $text
            );
        }, $text);
    }

    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    public function clearCache(): void
    {
        Cache::tags(['translations'])->flush();
    }
}
