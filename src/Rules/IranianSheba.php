<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;
use Zarbinco\PersianCore\Support\Validation\StrictValidationInput;

class IranianSheba implements ValidationRule
{
    public bool $implicit = true;

    public function __construct(
        private readonly ?PersianNumberNormalizerContract $numberNormalizer = null,
        private readonly ?PersianTextNormalizerContract $textNormalizer = null,
        private readonly ?bool $strict = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isEmpty($value)) {
            return;
        }

        $value = $this->stringValue($value);

        if ($this->strict() && ! StrictValidationInput::iranianSheba($value)) {
            $fail(__('persian-core::validation.iranian_sheba'));

            return;
        }

        $sheba = $this->normalize($value);

        if (preg_match('/^IR\d{24}$/', $sheba) !== 1 || ! $this->passesMod97($sheba)) {
            $fail(__('persian-core::validation.iranian_sheba'));
        }
    }

    private function normalize(string $value): string
    {
        $value = $this->textNormalizer()->normalize($value);
        $value = $this->numberNormalizer()->toEnglish($value);
        $value = strtoupper($value);

        return (string) preg_replace('/[\s\-\x{2010}-\x{2015}]+/u', '', $value);
    }

    private function passesMod97(string $iban): bool
    {
        $rearranged = substr($iban, 4).substr($iban, 0, 4);
        $remainder = 0;

        foreach (str_split($rearranged) as $character) {
            $digits = ctype_alpha($character) ? (string) (ord($character) - 55) : $character;

            foreach (str_split($digits) as $digit) {
                $remainder = ($remainder * 10 + (int) $digit) % 97;
            }
        }

        return $remainder === 1;
    }

    private function numberNormalizer(): PersianNumberNormalizerContract
    {
        return $this->numberNormalizer ?? new PersianNumberNormalizer;
    }

    private function textNormalizer(): PersianTextNormalizerContract
    {
        return $this->textNormalizer ?? new PersianTextNormalizer((array) config('persian-core.text', []));
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
