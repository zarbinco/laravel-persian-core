<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;

class PersianString
{
    public function __construct(
        private readonly string|int|float|null $value,
        private readonly PersianTextNormalizerContract $normalizer,
    ) {}

    public function normalize(): string
    {
        return $this->normalizer->normalize($this->value);
    }

    public function forStorage(): string
    {
        return $this->normalizer->forStorage($this->value);
    }

    public function forDisplay(): string
    {
        return $this->normalizer->forDisplay($this->value);
    }

    public function forSearch(): string
    {
        return $this->normalizer->forSearch($this->value);
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
