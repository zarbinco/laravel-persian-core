<?php

namespace Zarbinco\PersianCore\Contracts;

interface NumberFormatterContract
{
    public function format(string|int|float|null $value, ?string $digits = null): string;
}
