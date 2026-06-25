<?php

namespace Zarbinco\PersianCore\Formatters;

use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;

class NumberFormatter
{
    private readonly string $displayDigits;

    private readonly string $thousandsSeparator;

    private readonly string $decimalSeparator;

    /** @param array<string, mixed> $options */
    public function __construct(
        private readonly PersianNumberNormalizer $normalizer,
        array $options = [],
    ) {
        $this->displayDigits = $this->normalizeDigitsOption($options['display_digits'] ?? null, 'fa');
        $this->thousandsSeparator = $this->stringOption($options['thousands_separator'] ?? null, ',');
        $this->decimalSeparator = $this->stringOption($options['decimal_separator'] ?? null, '.');
    }

    public function format(string|int|float|null $value, ?string $digits = null): string
    {
        $number = $this->normalizer->clean($value);

        if ($number === '') {
            return '';
        }

        $isNegative = str_starts_with($number, '-');
        $unsigned = $isNegative ? substr($number, 1) : $number;
        [$integer, $decimal] = array_pad(explode('.', $unsigned, 2), 2, null);

        $integer = $integer === '' ? '0' : $integer;
        $formatted = ($isNegative ? '-' : '').$this->formatInteger($integer);

        if ($decimal !== null && $decimal !== '') {
            $formatted .= $this->decimalSeparator.$decimal;
        }

        return $this->targetDigits($digits) === 'fa'
            ? $this->normalizer->toPersian($formatted)
            : $formatted;
    }

    private function formatInteger(string $integer): string
    {
        $groups = [];

        while (strlen($integer) > 3) {
            array_unshift($groups, substr($integer, -3));
            $integer = substr($integer, 0, -3);
        }

        array_unshift($groups, $integer);

        return implode($this->thousandsSeparator, $groups);
    }

    private function targetDigits(?string $digits): string
    {
        return $this->normalizeDigitsOption($digits, $this->displayDigits);
    }

    private function normalizeDigitsOption(mixed $value, string $fallback): string
    {
        return is_string($value) && in_array($value, ['en', 'fa'], true) ? $value : $fallback;
    }

    private function stringOption(mixed $value, string $fallback): string
    {
        return is_string($value) && $value !== '' ? $value : $fallback;
    }
}
