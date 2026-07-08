<?php

namespace Zarbinco\PersianCore\Contracts;

interface PersianNumberNormalizerContract extends Normalizer
{
    public function toEnglish(string|int|float|null $value): string;

    public function toPersian(string|int|float|null $value): string;

    public function clean(string|int|float|null $value): string;

    public function digitsOnly(string|int|float|null $value): string;

    public function toInt(string|int|float|null $value): ?int;

    public function toFloat(string|int|float|null $value): ?float;

    public function isNumeric(string|int|float|null $value): bool;
}
