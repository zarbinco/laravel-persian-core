<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;

class IranianNationalCode implements ValidationRule
{
    public function __construct(
        private readonly ?PersianNumberNormalizer $normalizer = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isEmpty($value)) {
            return;
        }

        $digits = $this->normalizer()->digitsOnly($this->stringValue($value));

        if (strlen($digits) !== 10 || $this->isRepeated($digits) || ! $this->hasValidChecksum($digits)) {
            $fail(__('persian-core::validation.iranian_national_code'));
        }
    }

    private function hasValidChecksum(string $digits): bool
    {
        $sum = 0;

        for ($index = 0; $index < 9; $index++) {
            $sum += (int) $digits[$index] * (10 - $index);
        }

        $remainder = $sum % 11;
        $checkDigit = (int) $digits[9];

        return $remainder < 2
            ? $checkDigit === $remainder
            : $checkDigit === 11 - $remainder;
    }

    private function normalizer(): PersianNumberNormalizer
    {
        return $this->normalizer ?? new PersianNumberNormalizer;
    }

    private function isRepeated(string $digits): bool
    {
        return preg_match('/^(\d)\1{9}$/', $digits) === 1;
    }

    private function isEmpty(mixed $value): bool
    {
        return $this->emptyValuesPass() && ($value === null || $value === '');
    }

    private function emptyValuesPass(): bool
    {
        return (bool) config('persian-core.validation.empty_values_pass', true);
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
