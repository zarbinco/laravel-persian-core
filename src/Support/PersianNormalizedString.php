<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Contracts\PersianNormalizerPipelineContract;

class PersianNormalizedString
{
    public function __construct(
        private readonly string|int|float|null $value,
        private readonly PersianNormalizerPipelineContract $pipeline,
    ) {}

    public function forStorage(): string
    {
        return $this->pipeline->forStorage($this->value);
    }

    public function forDisplay(): string
    {
        return $this->pipeline->forDisplay($this->value);
    }

    public function forSearch(): string
    {
        return $this->pipeline->forSearch($this->value);
    }

    public function clean(): string
    {
        return $this->pipeline->clean($this->value);
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
