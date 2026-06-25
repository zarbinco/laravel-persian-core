<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;

class IranianSheba implements ValidationRule
{
    public function __construct(
        private readonly ?PersianNumberNormalizer $numberNormalizer = null,
        private readonly ?PersianTextNormalizer $textNormalizer = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isEmpty($value)) {
            return;
        }

        $sheba = $this->normalize($this->stringValue($value));

        if (preg_match('/^IR\d{24}$/', $sheba) !== 1 || ! $this->passesMod97($sheba)) {
            $fail(__('persian-core::validation.iranian_sheba'));
        }
    }

    private function normalize(string $value): string
    {
        $value = $this->textNormalizer()->normalize($value);
        $value = $this->numberNormalizer()->toEnglish($value);
        $value = strtoupper($value);

        return (string) preg_replace('/[\s\-]+/u', '', $value);
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

    private function numberNormalizer(): PersianNumberNormalizer
    {
        return $this->numberNormalizer ?? new PersianNumberNormalizer;
    }

    private function textNormalizer(): PersianTextNormalizer
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

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
