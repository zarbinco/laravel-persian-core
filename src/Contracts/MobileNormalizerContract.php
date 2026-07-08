<?php

namespace Zarbinco\PersianCore\Contracts;

interface MobileNormalizerContract
{
    public function normalize(string|int|float|null $value): string;

    public function national(string|int|float|null $value): string;

    public function clean(string|int|float|null $value): string;

    public function digits(string|int|float|null $value): string;
}
