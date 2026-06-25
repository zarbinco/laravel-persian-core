<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;

class PersianString
{
    public function __construct(
        private readonly string|int|float|null $value,
        private readonly PersianTextNormalizer $normalizer,
    ) {}

    public function normalize(): string
    {
        return $this->normalizer->normalize($this->value);
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
