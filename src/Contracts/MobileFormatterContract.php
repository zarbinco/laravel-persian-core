<?php

namespace Zarbinco\PersianCore\Contracts;

interface MobileFormatterContract
{
    public function international(string|int|float|null $value): string;

    public function e164(string|int|float|null $value): string;

    public function mask(string|int|float|null $value, ?string $mask = null): string;
}
