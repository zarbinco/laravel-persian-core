<?php

namespace Zarbinco\PersianCore;

use Zarbinco\PersianCore\Contracts\IranianBankDetectorContract;
use Zarbinco\PersianCore\Contracts\MobileFormatterContract;
use Zarbinco\PersianCore\Contracts\MobileNormalizerContract;
use Zarbinco\PersianCore\Contracts\MoneyFormatterContract;
use Zarbinco\PersianCore\Contracts\MoneyNormalizerContract;
use Zarbinco\PersianCore\Contracts\NumberFormatterContract;
use Zarbinco\PersianCore\Contracts\PersianNormalizerPipelineContract;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianSearchNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;
use Zarbinco\PersianCore\Normalizers\PersianSearchNormalizer;
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
    private readonly PersianSearchNormalizerContract $searchNormalizer;

    private readonly IranianBankDetectorContract $bankDetector;

    public function __construct(
        private readonly PersianTextNormalizerContract $textNormalizer,
        private readonly PersianNumberNormalizerContract $numberNormalizer,
        private readonly NumberFormatterContract $numberFormatter,
        private readonly MoneyNormalizerContract $moneyNormalizer,
        private readonly MoneyFormatterContract $moneyFormatter,
        private readonly MobileNormalizerContract $mobileNormalizer,
        private readonly MobileFormatterContract $mobileFormatter,
        private readonly PersianNormalizerPipelineContract $pipeline,
        ?PersianSearchNormalizerContract $searchNormalizer = null,
        ?IranianBankDetectorContract $bankDetector = null,
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
