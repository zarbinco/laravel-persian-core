<?php

namespace Zarbinco\PersianCore\Normalizers;

use Zarbinco\PersianCore\Contracts\Normalizer;

class PersianNumberNormalizer implements Normalizer
{
    private const ENGLISH_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    private const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    private const ARABIC_DIGITS = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    public function normalize(string|int|float|null $value): string
    {
        return $this->toEnglish($value);
    }

    public function toEnglish(string|int|float|null $value): string
    {
        return str_replace(
            array_merge(self::PERSIAN_DIGITS, self::ARABIC_DIGITS),
            array_merge(self::ENGLISH_DIGITS, self::ENGLISH_DIGITS),
            $this->stringValue($value),
        );
    }

    public function toPersian(string|int|float|null $value): string
    {
        return str_replace(
            array_merge(self::ENGLISH_DIGITS, self::ARABIC_DIGITS),
            array_merge(self::PERSIAN_DIGITS, self::PERSIAN_DIGITS),
            $this->stringValue($value),
        );
    }

    public function clean(string|int|float|null $value): string
    {
        $number = $this->toEnglish($value);
        $number = str_replace(['−', '٫'], ['-', '.'], $number);
        $number = str_replace(['٬', ',', '_'], '', $number);
        $number = (string) preg_replace('/(?<=\d)\s+(?=\d)/u', '', $number);

        preg_match('/-?(?:\d+(?:\.\d*)?|\.\d+)/u', $number, $matches);

        return $matches[0] ?? '';
    }

    public function digitsOnly(string|int|float|null $value): string
    {
        return (string) preg_replace('/\D+/u', '', $this->toEnglish($value));
    }

    public function toInt(string|int|float|null $value): ?int
    {
        $number = $this->clean($value);

        return $number === '' ? null : (int) $number;
    }

    public function toFloat(string|int|float|null $value): ?float
    {
        $number = $this->clean($value);

        return $number === '' ? null : (float) $number;
    }

    public function isNumeric(string|int|float|null $value): bool
    {
        $number = $this->clean($value);

        return $number !== '' && is_numeric($number);
    }

    private function stringValue(string|int|float|null $value): string
    {
        return $value === null ? '' : (string) $value;
    }
}
