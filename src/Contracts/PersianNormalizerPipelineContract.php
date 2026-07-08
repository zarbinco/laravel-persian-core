<?php

namespace Zarbinco\PersianCore\Contracts;

interface PersianNormalizerPipelineContract
{
    public function forStorage(string|int|float|null $value): string;

    public function forDisplay(string|int|float|null $value): string;

    public function forSearch(string|int|float|null $value): string;

    public function clean(string|int|float|null $value): string;
}
