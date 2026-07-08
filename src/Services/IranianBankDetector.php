<?php

namespace Zarbinco\PersianCore\Services;

use Zarbinco\PersianCore\Contracts\IranianBankDetectorContract;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Data\IranianBanks;
use Zarbinco\PersianCore\Support\IranianBank;

final class IranianBankDetector implements IranianBankDetectorContract
{
    /** @var array<int, array{slug: string, name: string, name_fa: string, card_bins: array<int, string>, sheba_codes: array<int, string>}> */
    private array $banks;

    /** @param array<int, array{slug: string, name: string, name_fa: string, card_bins: array<int, string>, sheba_codes: array<int, string>}>|null $banks */
    public function __construct(
        private readonly PersianNumberNormalizerContract $numberNormalizer,
        ?array $banks = null,
    ) {
        $this->banks = $banks ?? IranianBanks::all();
    }

    public function fromCard(string|int|float|null $value): ?IranianBank
    {
        $normalized = $this->normalizeCard($value);

        if (preg_match('/^\d{6,}/', $normalized) !== 1) {
            return null;
        }

        return $this->bankBySlug($this->slugByCardBin($normalized));
    }

    public function fromSheba(string|int|float|null $value): ?IranianBank
    {
        $normalized = $this->normalizeSheba($value);

        if (preg_match('/^IR\d{24}$/', $normalized) !== 1) {
            return null;
        }

        return $this->bankBySlug($this->slugByShebaCode(substr($normalized, 4, 3)));
    }

    public function normalizeCard(string|int|float|null $value): string
    {
        $card = $this->numberNormalizer->toEnglish($value);

        return (string) preg_replace('/[\s\-\x{2010}-\x{2015}]+/u', '', trim($card));
    }

    public function normalizeSheba(string|int|float|null $value): string
    {
        $sheba = strtoupper($this->numberNormalizer->toEnglish($value));

        return (string) preg_replace('/[\s\-\x{2010}-\x{2015}]+/u', '', trim($sheba));
    }

    private function slugByCardBin(string $card): ?string
    {
        $matchedSlug = null;
        $matchedLength = 0;

        foreach ($this->banks as $bank) {
            foreach ($bank['card_bins'] as $bin) {
                $length = strlen($bin);

                if ($length > $matchedLength && str_starts_with($card, $bin)) {
                    $matchedSlug = $bank['slug'];
                    $matchedLength = $length;
                }
            }
        }

        return $matchedSlug;
    }

    private function slugByShebaCode(string $code): ?string
    {
        foreach ($this->banks as $bank) {
            if (in_array($code, $bank['sheba_codes'], true)) {
                return $bank['slug'];
            }
        }

        return null;
    }

    private function bankBySlug(?string $slug): ?IranianBank
    {
        if ($slug === null) {
            return null;
        }

        foreach ($this->banks as $bank) {
            if ($bank['slug'] === $slug) {
                return IranianBank::fromArray($bank);
            }
        }

        return null;
    }
}
