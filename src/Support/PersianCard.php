<?php

namespace Zarbinco\PersianCore\Support;

use Zarbinco\PersianCore\Services\IranianBankDetector;

class PersianCard
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
        return $this->detector->normalizeCard($this->value);
    }

    public function bank(): ?IranianBank
    {
        return $this->detector->fromCard($this->value);
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
