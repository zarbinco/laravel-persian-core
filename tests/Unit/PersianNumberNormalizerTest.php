<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianNumberNormalizerTest extends TestCase
{
    public function test_it_converts_persian_and_arabic_digits_to_english(): void
    {
        $normalizer = new PersianNumberNormalizer;

        $this->assertSame('123456', $normalizer->toEnglish('۱۲۳٤٥۶'));
    }

    public function test_it_converts_english_digits_to_persian(): void
    {
        $normalizer = new PersianNumberNormalizer;

        $this->assertSame('۱۲۳۴۵۶', $normalizer->toPersian('123456'));
    }

    public function test_null_input_returns_empty_string(): void
    {
        $normalizer = new PersianNumberNormalizer;

        $this->assertSame('', $normalizer->toEnglish(null));
        $this->assertSame('', $normalizer->toPersian(null));
    }
}
