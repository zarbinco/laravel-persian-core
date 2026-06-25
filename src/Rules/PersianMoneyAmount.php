<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Normalizers\MoneyNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;

class PersianMoneyAmount implements ValidationRule
{
    public function __construct(
        private readonly ?MoneyNormalizer $normalizer = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isEmpty($value)) {
            return;
        }

        if ($this->normalizer()->value($this->stringValue($value)) === null) {
            $fail(__('persian-core::validation.persian_money_amount'));
        }
    }

    private function normalizer(): MoneyNormalizer
    {
        return $this->normalizer ?? new MoneyNormalizer(new PersianNumberNormalizer);
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
