<?php

namespace Zarbinco\PersianCore\Support;

final readonly class IranianBank
{
    /**
     * @param  array<int, string>  $cardBins
     * @param  array<int, string>  $shebaCodes
     */
    public function __construct(
        private string $slug,
        private string $name,
        private string $nameFa,
        private array $cardBins,
        private array $shebaCodes,
    ) {}

    /** @param array{slug: string, name: string, name_fa: string, card_bins: array<int, string>, sheba_codes: array<int, string>} $bank */
    public static function fromArray(array $bank): self
    {
        return new self(
            $bank['slug'],
            $bank['name'],
            $bank['name_fa'],
            array_values($bank['card_bins']),
            array_values($bank['sheba_codes']),
        );
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function nameFa(): string
    {
        return $this->nameFa;
    }

    /** @return array<int, string> */
    public function cardBins(): array
    {
        return $this->cardBins;
    }

    /** @return array<int, string> */
    public function shebaCodes(): array
    {
        return $this->shebaCodes;
    }

    /** @return array{slug: string, name: string, name_fa: string, card_bins: array<int, string>, sheba_codes: array<int, string>} */
    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'name_fa' => $this->nameFa,
            'card_bins' => $this->cardBins(),
            'sheba_codes' => $this->shebaCodes(),
        ];
    }
}
