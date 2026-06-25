<?php

namespace Zarbinco\PersianCore;

use Zarbinco\PersianCore\Formatters\MobileFormatter;
use Zarbinco\PersianCore\Formatters\NumberFormatter;
use Zarbinco\PersianCore\Normalizers\MobileNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianNormalizerPipeline;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;
use Zarbinco\PersianCore\Support\PersianMobile;
use Zarbinco\PersianCore\Support\PersianNormalizedString;
use Zarbinco\PersianCore\Support\PersianNumber;
use Zarbinco\PersianCore\Support\PersianString;

class PersianManager
{
    public function __construct(
        private readonly PersianTextNormalizer $textNormalizer,
        private readonly PersianNumberNormalizer $numberNormalizer,
        private readonly NumberFormatter $numberFormatter,
        private readonly MobileNormalizer $mobileNormalizer,
        private readonly MobileFormatter $mobileFormatter,
        private readonly PersianNormalizerPipeline $pipeline,
    ) {}

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

    public function normalize(string|int|float|null $value): PersianNormalizedString
    {
        return new PersianNormalizedString($value, $this->pipeline);
    }

    public function clean(string|int|float|null $value): string
    {
        return $this->pipeline->forStorage($value);
    }

    public function searchable(string|int|float|null $value): string
    {
        return $this->pipeline->forSearch($value);
    }
}
