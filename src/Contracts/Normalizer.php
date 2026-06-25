<?php

namespace Zarbinco\PersianCore\Contracts;

interface Normalizer
{
    public function normalize(string|int|float|null $value): string;
}
