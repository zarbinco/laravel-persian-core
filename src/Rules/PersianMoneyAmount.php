<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Contracts\MoneyNormalizerContract;
use Zarbinco\PersianCore\Normalizers\MoneyNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Support\Validation\StrictValidationInput;

class PersianMoneyAmount implements ValidationRule
{
    public bool $implicit = true;

    public function __construct(
        private readonly ?MoneyNormalizerContract $normalizer = null,
        private readonly ?bool $strict = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isEmpty($value)) {
            return;
        }

        $value = $this->stringValue($value);

        if ($this->strict() && ! StrictValidationInput::persianMoneyAmount($value)) {
            $fail(__('persian-core::validation.persian_money_amount'));

            return;
        }

        if ($this->normalizer()->value($value) === null) {
            $fail(__('persian-core::validation.persian_money_amount'));
        }
    }

    private function normalizer(): MoneyNormalizerContract
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

    private function strict(): bool
    {
        return $this->strict ?? (bool) config('persian-core.validation.strict', true);
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
