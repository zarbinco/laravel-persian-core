<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;

class IranianPostalCode implements ValidationRule
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

        if (strlen($digits) !== 10 || ($this->rejectRepeatedDigits() && $this->isRepeated($digits))) {
            $fail(__('persian-core::validation.iranian_postal_code'));
        }
    }

    private function normalizer(): PersianNumberNormalizer
    {
        return $this->normalizer ?? new PersianNumberNormalizer;
    }

    private function rejectRepeatedDigits(): bool
    {
        return (bool) config('persian-core.validation.iranian_postal_code.reject_repeated_digits', true);
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
