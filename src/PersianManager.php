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
use Zarbinco\PersianCore\Support\PersianMobile;
use Zarbinco\PersianCore\Support\PersianMoney;
use Zarbinco\PersianCore\Support\PersianNormalizedString;
use Zarbinco\PersianCore\Support\PersianNumber;
use Zarbinco\PersianCore\Support\PersianSearchString;
use Zarbinco\PersianCore\Support\PersianString;

class PersianManager
{
    private readonly PersianSearchNormalizer $searchNormalizer;

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
    ) {
        $this->searchNormalizer = $searchNormalizer ?? new PersianSearchNormalizer($this->numberNormalizer);
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

    public function clean(string|int|float|null $value): string
    {
        return $this->pipeline->forStorage($value);
    }

    public function searchable(string|int|float|null $value): string
    {
        return $this->searchNormalizer->normalize($value);
    }
}
