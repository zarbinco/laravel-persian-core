<?php

namespace Zarbinco\PersianCore\Contracts;

interface PersianTextNormalizerContract extends Normalizer
{
    public function forStorage(string|int|float|null $value): string;

    public function forDisplay(string|int|float|null $value): string;

    public function forSearch(string|int|float|null $value): string;
}
