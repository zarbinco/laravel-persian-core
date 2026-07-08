<?php

namespace Zarbinco\PersianCore\Contracts;

interface MoneyNormalizerContract
{
    public function clean(string|int|float|null $value): string;

    public function value(string|int|float|null $value): ?int;

    public function detectedCurrency(string|int|float|null $value): ?string;
}
