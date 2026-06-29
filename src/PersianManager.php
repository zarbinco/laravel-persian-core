<?php

namespace Zarbinco\PersianCore;

use Zarbinco\PersianCore\Formatters\MobileFormatter;
use Zarbinco\PersianCore\Formatters\MoneyFormatter;
use Zarbinco\PersianCore\Formatters\NumberFormatter;
use Zarbinco\PersianCore\Normalizers\MobileNormalizer;
use Zarbinco\PersianCore\Normalizers\MoneyNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianNormalizerPipeline;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianSearchNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;
use Zarbinco\PersianCore\Services\IranianBankDetector;
use Zarbinco\PersianCore\Support\IranianBank;
use Zarbinco\PersianCore\Support\PersianCard;
use Zarbinco\PersianCore\Support\PersianMobile;
use Zarbinco\PersianCore\Support\PersianMoney;
use Zarbinco\PersianCore\Support\PersianNormalizedString;
use Zarbinco\PersianCore\Support\PersianNumber;
use Zarbinco\PersianCore\Support\PersianSearchString;
use Zarbinco\PersianCore\Support\PersianSheba;
use Zarbinco\PersianCore\Support\PersianString;

class PersianManager
{
    private readonly PersianSearchNormalizer $searchNormalizer;

    private readonly IranianBankDetector $bankDetector;

    public function __construct(
        private readonly PersianTextNormalizer $textNormalizer,
        private readonly PersianNumberNormalizer $numberNormalizer,
        private readonly NumberFormatter $numberFormatter,
        private readonly MoneyNormalizer $moneyNormalizer,
        private readonly MoneyFormatter $moneyFormatter,
        private readonly MobileNormalizer $mobileNormalizer,
        private readonly MobileFormatter $mobileFormatter,
        private readonly PersianNormalizerPipeline $pipeline,
        ?PersianSearchNormalizer $searchNormalizer = null,
        ?IranianBankDetector $bankDetector = null,
    ) {
        $this->searchNormalizer = $searchNormalizer ?? new PersianSearchNormalizer($this->numberNormalizer);
        $this->bankDetector = $bankDetector ?? new IranianBankDetector($this->numberNormalizer);
    }

    public function text(string|int|float|null $value): PersianString
    {
        return new PersianString($value, $this->textNormalizer);
    }

    public function number(string|int|float|null $value): PersianNumber
    {
        return new PersianNumber($value, $this->numberNormalizer, $this->numberFormatter);
    }

    public function mobile(string|int|float|null $value): PersianMobile
    {
        return new PersianMobile($value, $this->mobileNormalizer, $this->mobileFormatter);
    }

    public function money(string|int|float|null $value): PersianMoney
    {
        return new PersianMoney($value, $this->moneyNormalizer, $this->moneyFormatter);
    }

    public function normalize(string|int|float|null $value): PersianNormalizedString
    {
        return new PersianNormalizedString($value, $this->pipeline);
    }

    public function search(string|int|float|null $value): PersianSearchString
    {
        return new PersianSearchString($value, $this->searchNormalizer);
    }

    public function card(string|int|float|null $value): PersianCard
    {
        return new PersianCard($value, $this->bankDetector);
    }

    public function sheba(string|int|float|null $value): PersianSheba
    {
        return new PersianSheba($value, $this->bankDetector);
    }

    public function clean(string|int|float|null $value): string
    {
        return $this->pipeline->forStorage($value);
    }

    public function searchable(string|int|float|null $value): string
    {
        return $this->searchNormalizer->normalize($value);
    }

    public function bankFromCard(string|int|float|null $value): ?IranianBank
    {
        return $this->bankDetector->fromCard($value);
    }

    public function bankFromSheba(string|int|float|null $value): ?IranianBank
    {
        return $this->bankDetector->fromSheba($value);
    }
}
