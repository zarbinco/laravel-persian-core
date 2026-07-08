<?php

namespace Zarbinco\PersianCore\Formatters;

use Zarbinco\PersianCore\Contracts\MobileFormatterContract;
use Zarbinco\PersianCore\Contracts\MobileNormalizerContract;

class MobileFormatter implements MobileFormatterContract
{
    private readonly string $countryCode;

    private readonly string $maskPattern;

    /** @param array<string, mixed> $options */
    public function __construct(
        private readonly MobileNormalizerContract $normalizer,
        array $options = [],
    ) {
        $iran = is_array($options['iran'] ?? null) ? $options['iran'] : [];

        $this->countryCode = $this->stringOption($iran['country_code'] ?? null, '98');
        $this->maskPattern = $this->maskOption($iran['mask_pattern'] ?? null);
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

        return $this->applyMask($national, $mask ?? $this->maskPattern);
    }

    private function stringOption(mixed $value, string $fallback): string
    {
        return is_string($value) && $value !== '' ? $value : $fallback;
    }

    private function maskOption(mixed $value): string
    {
        return is_string($value) && $this->isValidMaskPattern($value) ? $value : '0912***4567';
    }

    private function applyMask(string $national, string $pattern): string
    {
        $pattern = $this->maskOption($pattern);
        $masked = '';

        for ($index = 0; $index < 11; $index++) {
            $masked .= $pattern[$index] === '*' ? '*' : $national[$index];
        }

        return $masked;
    }

    private function isValidMaskPattern(string $pattern): bool
    {
        return strlen($pattern) === 11 && str_contains($pattern, '*');
    }
}
