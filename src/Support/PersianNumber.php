<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Contracts\NumberFormatterContract;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Formatters\NumberFormatter;

class PersianNumber
{
    private readonly NumberFormatterContract $formatter;

    public function __construct(
        private readonly string|int|float|null $value,
        private readonly PersianNumberNormalizerContract $normalizer,
        ?NumberFormatterContract $formatter = null,
    ) {
        $this->formatter = $formatter ?? new NumberFormatter($normalizer);
    }

    public function toEnglish(): string
    {
        return $this->normalizer->toEnglish($this->value);
    }

    public function toPersian(): string
    {
        return $this->normalizer->toPersian($this->value);
    }

    public function clean(): string
    {
        return $this->normalizer->clean($this->value);
    }

    public function digitsOnly(): string
    {
        return $this->normalizer->digitsOnly($this->value);
    }

    public function toInt(): ?int
    {
        return $this->normalizer->toInt($this->value);
    }

    public function toFloat(): ?float
    {
        return $this->normalizer->toFloat($this->value);
    }

    public function isNumeric(): bool
    {
        return $this->normalizer->isNumeric($this->value);
    }

    public function format(?string $digits = null): string
    {
        return $this->formatter->format($this->value, $digits);
    }

    public function value(): string
    {
        return $this->stringValue();
    }

    public function __toString(): string
    {
        return $this->value();
    }

    private function stringValue(): string
    {
        return $this->value === null ? '' : (string) $this->value;
    }
}
