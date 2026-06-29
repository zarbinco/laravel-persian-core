<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Normalizers\MobileNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Support\Validation\StrictValidationInput;

class IranianMobile implements ValidationRule
{
    public bool $implicit = true;

    public function __construct(
        private readonly ?MobileNormalizer $normalizer = null,
        private readonly ?bool $strict = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isEmpty($value)) {
            return;
        }

        $value = $this->stringValue($value);

        if ($this->strict() && ! StrictValidationInput::iranianMobile($value)) {
            $fail(__('persian-core::validation.iranian_mobile'));

            return;
        }

        $mobile = $this->normalizer()->national($value);

        if (preg_match('/^09\d{9}$/', $mobile) !== 1) {
            $fail(__('persian-core::validation.iranian_mobile'));
        }
    }

    private function normalizer(): MobileNormalizer
    {
        return $this->normalizer ?? new MobileNormalizer(new PersianNumberNormalizer, (array) config('persian-core.mobile', []));
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
