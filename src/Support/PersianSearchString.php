<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Normalizers\PersianSearchNormalizer;

class PersianSearchString
{
    public function __construct(
        private readonly string|int|float|null $value,
        private readonly PersianSearchNormalizer $normalizer,
    ) {}

    public function normalize(): string
    {
        return $this->normalizer->normalize($this->value);
    }

    /** @return array<int, string> */
    public function tokens(): array
    {
        return $this->normalizer->tokens($this->value);
    }

    public function value(): string
    {
        return $this->normalize();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
