<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Contracts\MobileFormatterContract;
use Zarbinco\PersianCore\Contracts\MobileNormalizerContract;

class PersianMobile
{
    public function __construct(
        private readonly string|int|float|null $value,
        private readonly MobileNormalizerContract $normalizer,
        private readonly MobileFormatterContract $formatter,
    ) {}

    public function normalize(): string
    {
        return $this->normalizer->normalize($this->value);
    }

    public function national(): string
    {
        return $this->normalizer->national($this->value);
    }

    public function international(): string
    {
        return $this->formatter->international($this->value);
    }

    public function e164(): string
    {
        return $this->formatter->e164($this->value);
    }

    public function mask(?string $mask = null): string
    {
        return $this->formatter->mask($this->value, $mask);
    }

    public function value(): string
    {
        return $this->value === null ? '' : (string) $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
