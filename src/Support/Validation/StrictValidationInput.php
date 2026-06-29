<?php

namespace Zarbinco\PersianCore\Support\Validation;

final class StrictValidationInput
{
    /** @var array<int, string> */
    private const ENGLISH_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /** @var array<int, string> */
    private const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    /** @var array<int, string> */
    private const ARABIC_DIGITS = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    public static function iranianMobile(string $value): bool
    {
        $value = self::toEnglishDigits(trim($value));

        if ($value === '' || preg_match('/[^0-9+\s\-\x{2010}-\x{2015}()]/u', $value) === 1) {
            return false;
        }

        if (substr_count($value, '+') > 1 || (str_contains($value, '+') && ! str_starts_with($value, '+'))) {
            return false;
        }

        if (
            (str_contains($value, '(') || str_contains($value, ')'))
            && preg_match('/^\(09[0-9]{2}\)[\s\-\x{2010}-\x{2015}]*[0-9]{3}[\s\-\x{2010}-\x{2015}]*[0-9]{4}$/u', $value) !== 1
        ) {
            return false;
        }

        $compact = (string) preg_replace('/[\s\-\x{2010}-\x{2015}()]+/u', '', $value);

        return preg_match('/^(?:09[0-9]{9}|9[0-9]{9}|\+989[0-9]{9}|00989[0-9]{9}|989[0-9]{9})$/', $compact) === 1;
    }

    public static function digitsWithSpacesOrDashes(string $value, int $digits): bool
    {
        $value = self::toEnglishDigits(trim($value));

        if ($value === '' || preg_match('/[^0-9\s\-\x{2010}-\x{2015}]/u', $value) === 1) {
            return false;
        }

        $compact = self::removeSpacesAndDashes($value);

        return strlen($compact) === $digits;
    }

    public static function iranianCardNumber(string $value): bool
    {
        $value = self::toEnglishDigits(trim($value));

        return preg_match('/^[0-9]{4}(?:[\s\-\x{2010}-\x{2015}]*[0-9]{4}){3}$/u', $value) === 1;
    }

    public static function iranianSheba(string $value): bool
    {
        $value = self::toEnglishDigits(trim($value));

        if ($value === '' || preg_match('/[^A-Za-z0-9\s\-\x{2010}-\x{2015}]/u', $value) === 1) {
            return false;
        }

        $compact = strtoupper(self::removeSpacesAndDashes($value));

        return preg_match('/^IR[0-9]{24}$/', $compact) === 1;
    }

    public static function persianMoneyAmount(string $value): bool
    {
        $value = self::toEnglishDigits(trim($value));

        if ($value === '') {
            return false;
        }

        $currencyLabel = '(?:تومان|تومن|ریال|ريال|toman|rial)';
        $thousandSeparator = '[\s,\x{066C}]';
        $number = '(?:[0-9]{1,3}(?:'.$thousandSeparator.'[0-9]{3})+|[0-9]+)';

        return preg_match('/^[\-−]?'.$number.'(?:[\.٫][0-9]+)?(?:\s*'.$currencyLabel.')?$/iu', $value) === 1;
    }

    private static function removeSpacesAndDashes(string $value): string
    {
        return (string) preg_replace('/[\s\-\x{2010}-\x{2015}]+/u', '', $value);
    }

    private static function toEnglishDigits(string $value): string
    {
        return str_replace(
            array_merge(self::PERSIAN_DIGITS, self::ARABIC_DIGITS),
            array_merge(self::ENGLISH_DIGITS, self::ENGLISH_DIGITS),
            $value,
        );
    }
}
