<?php

namespace Zarbinco\PersianCore\Contracts;

interface MoneyFormatterContract
{
    public function format(string|int|float|null $amount, ?string $currency = null, ?string $digits = null): string;

    public function toman(string|int|float|null $amount, ?string $digits = null): string;

    public function rial(string|int|float|null $amount, ?string $digits = null): string;

    public function convertRialToToman(?int $rial): ?int;

    public function convertTomanToRial(?int $toman): ?int;

    public function defaultCurrency(): string;
}
