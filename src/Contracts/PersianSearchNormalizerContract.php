<?php

namespace Zarbinco\PersianCore\Contracts;

interface PersianSearchNormalizerContract extends Normalizer
{
    /** @return array<int, string> */
    public function tokens(string|int|float|null $value): array;
}
