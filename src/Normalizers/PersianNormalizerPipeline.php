<?php

namespace Zarbinco\PersianCore\Normalizers;

use Zarbinco\PersianCore\Contracts\PersianNormalizerPipelineContract;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianSearchNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;

class PersianNormalizerPipeline implements PersianNormalizerPipelineContract
{
    private readonly string $storageDigits;

    private readonly string $displayDigits;

    private readonly PersianSearchNormalizerContract $searchNormalizer;

    /** @param array<string, mixed> $numberOptions */
    public function __construct(
        private readonly PersianTextNormalizerContract $textNormalizer,
        private readonly PersianNumberNormalizerContract $numberNormalizer,
        array $numberOptions = [],
        ?PersianSearchNormalizerContract $searchNormalizer = null,
    ) {
        $this->storageDigits = $this->normalizeDigitsOption($numberOptions['storage_digits'] ?? null, 'en');
        $this->displayDigits = $this->normalizeDigitsOption($numberOptions['display_digits'] ?? null, 'fa');
        $this->searchNormalizer = $searchNormalizer ?? new PersianSearchNormalizer($numberNormalizer);
    }

    public function forStorage(string|int|float|null $value): string
    {
        return $this->normalizeDigits($this->textNormalizer->normalize($value), $this->storageDigits);
    }

    public function forDisplay(string|int|float|null $value): string
    {
        return $this->normalizeDigits($this->textNormalizer->forDisplay($value), $this->displayDigits);
    }

    public function forSearch(string|int|float|null $value): string
    {
        return $this->searchNormalizer->normalize($value);
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
