<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Support\Validation\StrictValidationInput;

class IranianPostalCode implements ValidationRule
{
    public bool $implicit = true;

    public function __construct(
        private readonly ?PersianNumberNormalizerContract $normalizer = null,
        private readonly ?bool $strict = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isEmpty($value)) {
            return;
        }

        $value = $this->stringValue($value);

        if ($this->strict() && ! StrictValidationInput::digitsWithSpacesOrDashes($value, 10)) {
            $fail(__('persian-core::validation.iranian_postal_code'));

            return;
        }

        $digits = $this->normalizer()->digitsOnly($value);

        if (strlen($digits) !== 10 || ($this->rejectRepeatedDigits() && $this->isRepeated($digits))) {
            $fail(__('persian-core::validation.iranian_postal_code'));
        }
    }

    private function normalizer(): PersianNumberNormalizerContract
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

    private function strict(): bool
    {
        return $this->strict ?? (bool) config('persian-core.validation.strict', true);
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
