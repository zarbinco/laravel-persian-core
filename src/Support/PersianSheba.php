<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Services\IranianBankDetector;

class PersianSheba
{
    public function __construct(
        private readonly string|int|float|null $value,
        private readonly IranianBankDetector $detector,
    ) {}

    public function value(): string
    {
        return $this->value === null ? '' : (string) $this->value;
    }

    public function normalized(): string
    {
        return $this->detector->normalizeSheba($this->value);
    }

    public function bank(): ?IranianBank
    {
        return $this->detector->fromSheba($this->value);
    }

    public function bankSlug(): ?string
    {
        return $this->bank()?->slug();
    }

    public function bankName(): ?string
    {
        return $this->bank()?->name();
    }

    public function bankNameFa(): ?string
    {
        return $this->bank()?->nameFa();
    }
}
