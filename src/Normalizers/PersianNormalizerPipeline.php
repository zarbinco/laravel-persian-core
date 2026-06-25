<?php

namespace Zarbinco\PersianCore\Normalizers;

class PersianNormalizerPipeline
{
    private readonly string $storageDigits;

    private readonly string $displayDigits;

    /** @param array<string, mixed> $numberOptions */
    public function __construct(
        private readonly PersianTextNormalizer $textNormalizer,
        private readonly PersianNumberNormalizer $numberNormalizer,
        array $numberOptions = [],
    ) {
        $this->storageDigits = $this->normalizeDigitsOption($numberOptions['storage_digits'] ?? null, 'en');
        $this->displayDigits = $this->normalizeDigitsOption($numberOptions['display_digits'] ?? null, 'fa');
    }

    public function forStorage(string|int|float|null $value): string
    {
        return $this->normalizeDigits($this->textNormalizer->normalize($value), $this->storageDigits);
    }

    public function forDisplay(string|int|float|null $value): string
    {
        return $this->normalizeDigits($this->textNormalizer->normalize($value), $this->displayDigits);
    }

    public function clean(string|int|float|null $value): string
    {
        return $this->forStorage($value);
    }

    private function normalizeDigits(string $value, string $target): string
    {
        return $target === 'fa'
            ? $this->numberNormalizer->toPersian($value)
            : $this->numberNormalizer->toEnglish($value);
    }

    private function normalizeDigitsOption(mixed $value, string $fallback): string
    {
        return is_string($value) && in_array($value, ['en', 'fa'], true) ? $value : $fallback;
    }
}
