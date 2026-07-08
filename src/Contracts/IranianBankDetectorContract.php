<?php

namespace Zarbinco\PersianCore\Contracts;

use Zarbinco\PersianCore\Support\IranianBank;

interface IranianBankDetectorContract
{
    public function fromCard(string|int|float|null $value): ?IranianBank;

    public function fromSheba(string|int|float|null $value): ?IranianBank;

    public function normalizeCard(string|int|float|null $value): string;

    public function normalizeSheba(string|int|float|null $value): string;
}
