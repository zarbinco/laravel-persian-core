<?php

namespace Zarbinco\PersianCore\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;

class PersianAlphaNum implements ValidationRule
{
    private const PERSIAN_LETTERS = '\x{0621}-\x{063A}\x{0641}-\x{064A}\x{066E}-\x{066F}\x{0671}-\x{06D3}\x{06FA}-\x{06FC}';

    public function __construct(
        private readonly ?PersianTextNormalizerContract $textNormalizer = null,
        private readonly ?PersianNumberNormalizerContract $numberNormalizer = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isEmpty($value)) {
            return;
        }

        $text = $this->textNormalizer()->normalize($this->stringValue($value));
        $text = $this->numberNormalizer()->toEnglish($text);

        if ($text === '' || preg_match('/^['.self::PERSIAN_LETTERS.'0-9\s\x{200C}]+$/u', $text) !== 1) {
            $fail(__('persian-core::validation.persian_alpha_num'));
        }
    }

    private function textNormalizer(): PersianTextNormalizerContract
    {
        return $this->textNormalizer ?? new PersianTextNormalizer((array) config('persian-core.text', []));
    }

    private function numberNormalizer(): PersianNumberNormalizerContract
    {
        return $this->numberNormalizer ?? new PersianNumberNormalizer;
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
