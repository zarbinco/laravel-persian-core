<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Support\Validation\StrictValidationInput;

class IranianCardNumber implements ValidationRule
{
    public bool $implicit = true;

    /** @var array<int, string> */
    private const IRANIAN_BINS = [
        '603799',
        '589210',
        '627648',
        '627961',
        '603770',
        '639346',
        '502229',
        '627412',
        '622106',
        '610433',
        '621986',
        '639607',
        '636214',
        '502806',
        '502908',
        '627488',
        '505785',
        '505416',
        '505801',
        '606373',
    ];

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

        if ($this->strict() && ! StrictValidationInput::iranianCardNumber($value)) {
            $fail(__('persian-core::validation.iranian_card_number'));

            return;
        }

        $digits = $this->normalizer()->digitsOnly($value);

        if (
            strlen($digits) !== 16
            || ($this->requireLuhn() && ! $this->passesLuhn($digits))
            || ($this->requireIranianBin() && ! $this->hasIranianBin($digits))
        ) {
            $fail(__('persian-core::validation.iranian_card_number'));
        }
    }

    private function passesLuhn(string $digits): bool
    {
        $sum = 0;
        $double = false;

        for ($index = strlen($digits) - 1; $index >= 0; $index--) {
            $number = (int) $digits[$index];

            if ($double) {
                $number *= 2;

                if ($number > 9) {
                    $number -= 9;
                }
            }

            $sum += $number;
            $double = ! $double;
        }

        return $sum % 10 === 0;
    }

    private function hasIranianBin(string $digits): bool
    {
        return in_array(substr($digits, 0, 6), self::IRANIAN_BINS, true);
    }

    private function normalizer(): PersianNumberNormalizerContract
    {
        return $this->normalizer ?? new PersianNumberNormalizer;
    }

    private function requireLuhn(): bool
    {
        return (bool) config('persian-core.validation.iranian_card_number.require_luhn', true);
    }

    private function requireIranianBin(): bool
    {
        return (bool) config('persian-core.validation.iranian_card_number.require_iranian_bin', false);
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
