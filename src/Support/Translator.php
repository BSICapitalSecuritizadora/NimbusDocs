<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Translator class for internationalization (i18n)
 */
class Translator
{
    private static string $locale = 'pt-BR';

    private static array $translations = [];

    private static string $langPath = '';

    /**
     * Initialize the translator
     */
    public static function init(string $langPath, string $defaultLocale = 'pt-BR'): void
    {
        self::$langPath = rtrim($langPath, '/');
        self::setLocale($defaultLocale);
    }

    /**
     * Set the current locale
     */
    public static function setLocale(string $locale): void
    {
        $validLocales = ['pt-BR', 'en', 'es'];

        if (in_array($locale, $validLocales)) {
            self::$locale = $locale;
            self::loadTranslations($locale);
        }
    }

    /**
     * Get the current locale
     */
    public static function getLocale(): string
    {
        return self::$locale;
    }

    /**
     * Get available locales
     */
    public static function getAvailableLocales(): array
    {
        return [
            'pt-BR' => 'Português (Brasil)',
            'en' => 'English',
            'es' => 'Español',
        ];
    }

    /**
     * Load translations for a locale
     */
    private static function loadTranslations(string $locale): void
    {
        $file = self::$langPath . '/' . $locale . '/messages.php';

        if (file_exists($file)) {
            self::$translations[$locale] = require $file;
        } else {
            self::$translations[$locale] = [];
        }
    }

    /**
     * Translate a key
     *
     * @param string $key Translation key (supports dot notation: 'auth.login')
     * @param array $params Parameters to replace in the translation
     * @return string Translated string or key if not found
     */
    public static function trans(string $key, array $params = []): string
    {
        $locale = self::$locale;

        // Ensure translations are loaded
        if (!isset(self::$translations[$locale])) {
            self::loadTranslations($locale);
        }

        // Get translation
        $translation = self::get(self::$translations[$locale] ?? [], $key);

        // Fallback to English if not found
        if ($translation === null && $locale !== 'en') {
            if (!isset(self::$translations['en'])) {
                self::loadTranslations('en');
            }
            $translation = self::get(self::$translations['en'] ?? [], $key);
        }

        // Return key if not found
        if ($translation === null) {
            return $key;
        }

        // Replace parameters
        foreach ($params as $paramKey => $value) {
            $translation = str_replace(':' . $paramKey, (string) $value, $translation);
        }

        return $translation;
    }

    /**
     * Get a nested value using dot notation
     */
    private static function get(array $array, string $key): ?string
    {
        $keys = explode('.', $key);
        $value = $array;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return null;
            }
            $value = $value[$k];
        }

        return is_string($value) ? $value : null;
    }
}

/**
 * Helper function for translations
 */
function __($key, array $params = []): string
{
    return Translator::trans($key, $params);
}
