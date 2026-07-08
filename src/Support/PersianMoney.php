<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Contracts\MoneyFormatterContract;
use Zarbinco\PersianCore\Contracts\MoneyNormalizerContract;

class PersianMoney
{
    private ?string $sourceCurrency = null;

    public function __construct(
        private readonly string|int|float|null $value,
        private readonly MoneyNormalizerContract $normalizer,
        private readonly MoneyFormatterContract $formatter,
    ) {}

    public function clean(): string
    {
        return $this->normalizer->clean($this->value);
    }

    public function value(): ?int
    {
        return $this->normalizer->value($this->value);
    }

    public function detectedCurrency(): ?string
    {
        return $this->normalizer->detectedCurrency($this->value);
    }

    public function format(?string $currency = null, ?string $digits = null): string
    {
        return $this->formatter->format($this->value, $currency ?? $this->sourceCurrency(), $digits);
    }

    public function toman(?string $digits = null): string
    {
        return $this->formatter->toman($this->value, $digits);
    }

    public function rial(?string $digits = null): string
    {
        return $this->formatter->rial($this->value, $digits);
    }

    public function fromRial(): self
    {
        $this->sourceCurrency = 'rial';

        return $this;
    }

    public function fromToman(): self
    {
        $this->sourceCurrency = 'toman';

        return $this;
    }

    public function toRial(): ?int
    {
        $value = $this->value();

        return $this->sourceCurrency() === 'toman'
            ? $this->formatter->convertTomanToRial($value)
            : $value;
    }

    public function toToman(): ?int
    {
        $value = $this->value();

        return $this->sourceCurrency() === 'rial'
            ? $this->formatter->convertRialToToman($value)
            : $value;
    }

    public function formatRial(?string $digits = null): string
    {
        return $this->formatter->rial($this->toRial(), $digits);
    }

    public function formatToman(?string $digits = null): string
    {
        return $this->formatter->toman($this->toToman(), $digits);
    }

    public function __toString(): string
    {
        return $this->format();
    }

    private function sourceCurrency(): string
    {
        return $this->sourceCurrency
            ?? $this->detectedCurrency()
            ?? $this->formatter->defaultCurrency();
    }
}
