<?php

namespace Zarbinco\PersianCore\Formatters;

use Zarbinco\PersianCore\Normalizers\MobileNormalizer;

class MobileFormatter
{
    private readonly string $countryCode;

    /** @param array<string, mixed> $options */
    public function __construct(
        private readonly MobileNormalizer $normalizer,
        array $options = [],
    ) {
        $iran = is_array($options['iran'] ?? null) ? $options['iran'] : [];

        $this->countryCode = $this->stringOption($iran['country_code'] ?? null, '98');
    }

    public function international(string|int|float|null $value): string
    {
        $national = $this->normalizer->national($value);

        if (preg_match('/^09\d{9}$/', $national) === 1) {
            return '+'.$this->countryCode.substr($national, 1);
        }

        return $this->normalizer->clean($value);
    }

    public function e164(string|int|float|null $value): string
    {
        return $this->international($value);
    }

    public function mask(string|int|float|null $value, ?string $mask = null): string
    {
        $national = $this->normalizer->national($value);

        if (preg_match('/^09\d{9}$/', $national) !== 1) {
            return $national;
        }

        return substr($national, 0, 4).'***'.substr($national, -4);
    }

    private function stringOption(mixed $value, string $fallback): string
    {
        return is_string($value) && $value !== '' ? $value : $fallback;
    }
}
