<?php

namespace Zarbinco\PersianCore\Support;

use Composer\InstalledVersions;
use Throwable;

class ConfigValidator
{
    /** @return array<int, array{name: string, status: string, message: string, value: mixed}> */
    public function checks(): array
    {
        return [
            $this->phpVersion(),
            $this->laravelApplication(),
            $this->packageVersion(),
            $this->configLoaded(),
            $this->inList('text.search.zwnj', config('persian-core.text.search.zwnj'), ['preserve', 'remove', 'space']),
            $this->booleanCheck('text.search.normalize_madda_alef', config('persian-core.text.search.normalize_madda_alef')),
            $this->inList('numbers.storage_digits', config('persian-core.numbers.storage_digits'), ['en', 'fa']),
            $this->inList('numbers.display_digits', config('persian-core.numbers.display_digits'), ['en', 'fa']),
            $this->stringCheck('numbers.thousands_separator', config('persian-core.numbers.thousands_separator')),
            $this->stringCheck('numbers.decimal_separator', config('persian-core.numbers.decimal_separator')),
            $this->inList('mobile.default_country', config('persian-core.mobile.default_country'), ['IR']),
            $this->inList('money.default_currency', config('persian-core.money.default_currency'), ['toman', 'rial']),
            $this->inList('money.display_digits', config('persian-core.money.display_digits'), ['en', 'fa']),
            $this->positiveInteger('money.rial_to_toman_rate', config('persian-core.money.rial_to_toman_rate')),
            $this->booleanCheck('banks.unknown_returns_null', config('persian-core.banks.unknown_returns_null')),
            $this->booleanCheck('validation.strict', config('persian-core.validation.strict')),
            $this->booleanCheck('validation.empty_values_pass', config('persian-core.validation.empty_values_pass')),
            $this->booleanCheck('validation.iranian_postal_code.reject_repeated_digits', config('persian-core.validation.iranian_postal_code.reject_repeated_digits')),
            $this->booleanCheck('validation.iranian_card_number.require_luhn', config('persian-core.validation.iranian_card_number.require_luhn')),
            $this->booleanCheck('validation.iranian_card_number.require_iranian_bin', config('persian-core.validation.iranian_card_number.require_iranian_bin')),
            $this->translationLoaded(),
            $this->intlExtension(),
        ];
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function phpVersion(): array
    {
        return [
            'name' => 'php.version',
            'status' => version_compare(PHP_VERSION, '8.2.0', '>=') ? 'ok' : 'error',
            'message' => version_compare(PHP_VERSION, '8.2.0', '>=') ? 'PHP version is supported.' : 'PHP 8.2 or newer is required.',
            'value' => PHP_VERSION,
        ];
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function laravelApplication(): array
    {
        return [
            'name' => 'laravel.application',
            'status' => 'ok',
            'message' => 'Laravel application is available.',
            'value' => app()->version(),
        ];
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function packageVersion(): array
    {
        $version = 'unknown';

        if (class_exists(InstalledVersions::class)) {
            try {
                $version = InstalledVersions::getPrettyVersion('zarbinco/laravel-persian-core') ?? 'unknown';
            } catch (Throwable) {
                $version = 'unknown';
            }
        }

        return [
            'name' => 'package.version',
            'status' => $version === 'unknown' ? 'info' : 'ok',
            'message' => $version === 'unknown' ? 'Package version could not be detected.' : 'Package version detected.',
            'value' => $version,
        ];
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function configLoaded(): array
    {
        $loaded = is_array(config('persian-core'));

        return [
            'name' => 'config.loaded',
            'status' => $loaded ? 'ok' : 'error',
            'message' => $loaded ? 'Package config is loaded.' : 'Package config is not loaded.',
            'value' => $loaded ? 'loaded' : 'missing',
        ];
    }

    /**
     * @param  array<int, string>  $allowed
     * @return array{name: string, status: string, message: string, value: mixed}
     */
    private function inList(string $name, mixed $value, array $allowed): array
    {
        $valid = is_string($value) && in_array($value, $allowed, true);

        return [
            'name' => $name,
            'status' => $valid ? 'ok' : 'error',
            'message' => $valid ? 'Value is valid.' : 'Value must be one of: '.implode(', ', $allowed).'.',
            'value' => $value,
        ];
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function stringCheck(string $name, mixed $value): array
    {
        return [
            'name' => $name,
            'status' => is_string($value) ? 'ok' : 'error',
            'message' => is_string($value) ? 'Value is a string.' : 'Value must be a string.',
            'value' => $value,
        ];
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function positiveInteger(string $name, mixed $value): array
    {
        return [
            'name' => $name,
            'status' => is_int($value) && $value > 0 ? 'ok' : 'error',
            'message' => is_int($value) && $value > 0 ? 'Value is a positive integer.' : 'Value must be a positive integer.',
            'value' => $value,
        ];
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function booleanCheck(string $name, mixed $value): array
    {
        return [
            'name' => $name,
            'status' => is_bool($value) ? 'ok' : 'error',
            'message' => is_bool($value) ? 'Value is boolean.' : 'Value must be boolean.',
            'value' => $value,
        ];
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function translationLoaded(): array
    {
        $key = 'persian-core::validation.iranian_mobile';
        $message = __($key);
        $loaded = $message !== $key;

        return [
            'name' => 'translations.validation',
            'status' => $loaded ? 'ok' : 'error',
            'message' => $loaded ? 'Package validation translations are loadable.' : 'Package validation translations are not loadable.',
            'value' => $loaded ? 'loaded' : 'missing',
        ];
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function intlExtension(): array
    {
        $loaded = extension_loaded('intl');

        return [
            'name' => 'extension.intl',
            'status' => $loaded ? 'ok' : 'info',
            'message' => $loaded ? 'ext-intl is installed.' : 'ext-intl is optional and not installed.',
            'value' => $loaded ? 'installed' : 'missing',
        ];
    }
}
