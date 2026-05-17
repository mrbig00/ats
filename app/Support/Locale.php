<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Locale
{
    /**
     * @return list<string>
     */
    public static function supportedCodes(): array
    {
        return array_keys(config('locales.supported', []));
    }

    public static function isSupported(string $locale): bool
    {
        return in_array(self::normalize($locale), self::supportedCodes(), true);
    }

    public static function normalize(string $locale): string
    {
        $locale = strtolower(trim($locale));

        if (str_contains($locale, '_')) {
            $locale = explode('_', $locale)[0];
        }

        if (str_contains($locale, '-')) {
            $locale = explode('-', $locale)[0];
        }

        return $locale;
    }

    public static function label(string $code): string
    {
        $key = 'locale.'.$code;
        $translated = __($key);

        if ($translated !== $key) {
            return $translated;
        }

        return config('locales.supported.'.$code, $code);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::supportedCodes() as $code) {
            $options[$code] = self::label($code);
        }

        return $options;
    }

    public static function resolve(Request $request): string
    {
        $user = $request->user();

        if ($user !== null) {
            $locale = filled($user->language) && self::isSupported($user->language)
                ? self::normalize($user->language)
                : (string) config('app.locale');

            self::apply($locale);

            return $locale;
        }

        $sessionKey = (string) config('locales.session_key', 'locale');
        $sessionLocale = $request->session()->get($sessionKey);

        if (is_string($sessionLocale) && self::isSupported($sessionLocale)) {
            $locale = self::normalize($sessionLocale);
            App::setLocale($locale);

            return $locale;
        }

        $preferred = $request->getPreferredLanguage(self::supportedCodes());

        if ($preferred !== null && self::isSupported($preferred)) {
            $locale = self::normalize($preferred);
            self::apply($locale);

            return $locale;
        }

        $locale = (string) config('locales.guest_fallback', 'en');
        self::apply($locale);

        return $locale;
    }

    public static function apply(string $locale): void
    {
        $locale = self::normalize($locale);

        if (! self::isSupported($locale)) {
            $locale = (string) config('locales.guest_fallback', 'en');
        }

        App::setLocale($locale);
        Session::put((string) config('locales.session_key', 'locale'), $locale);
    }
}
