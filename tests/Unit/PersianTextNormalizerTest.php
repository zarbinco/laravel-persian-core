<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianTextNormalizerTest extends TestCase
{
    public function test_it_normalizes_arabic_yeh_and_kaf(): void
    {
        $normalizer = new PersianTextNormalizer;

        $this->assertSame('علی کاظمی', $normalizer->normalize('علي كاظمي'));
    }

    public function test_it_preserves_numbers(): void
    {
        $normalizer = new PersianTextNormalizer;

        $this->assertSame('علی ۱۲۳', $normalizer->normalize('علي ۱۲۳'));
    }

    public function test_it_preserves_zwnj(): void
    {
        $normalizer = new PersianTextNormalizer;

        $this->assertSame("می\u{200C}روم", $normalizer->normalize("می\u{200C}روم"));
    }

    public function test_it_removes_arabic_diacritics(): void
    {
        $normalizer = new PersianTextNormalizer;

        $this->assertSame('علی', $normalizer->normalize("ع\u{064E}ل\u{0650}ي\u{0651}"));
    }

    public function test_it_removes_tatweel(): void
    {
        $normalizer = new PersianTextNormalizer;

        $this->assertSame('علی', $normalizer->normalize('عــــلی'));
    }

    public function test_it_removes_problematic_invisible_characters_but_preserves_zwnj(): void
    {
        $normalizer = new PersianTextNormalizer;

        $this->assertSame(
            "می\u{200C}روم",
            $normalizer->normalize("\u{200B}می\u{200C}روم\u{200D}\u{FEFF}"),
        );
    }
}
