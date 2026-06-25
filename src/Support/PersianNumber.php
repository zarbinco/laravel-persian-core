<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;

class PersianNumber
{
    public function __construct(
        private readonly string|int|float|null $value,
        private readonly PersianNumberNormalizer $normalizer,
    ) {}

    public function toEnglish(): string
    {
        return $this->normalizer->toEnglish($this->value);
    }

    public function toPersian(): string
    {
        return $this->normalizer->toPersian($this->value);
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
