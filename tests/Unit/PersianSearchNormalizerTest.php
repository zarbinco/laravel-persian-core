<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianSearchNormalizer;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianSearchNormalizerTest extends TestCase
{
    public function test_it_normalizes_arabic_yeh_and_kaf(): void
    {
        $this->assertSame('کیک شکلاتی', $this->normalizer()->normalize('كیك شکلاتي'));
    }

    public function test_it_removes_diacritics_and_tatweel(): void
    {
        $this->assertSame('علی', $this->normalizer()->normalize("عــــل\u{0650}ي"));
    }

    public function test_it_handles_zwnj_by_config(): void
    {
        $this->assertSame('آب میوه', $this->normalizer(['search' => ['normalize_madda_alef' => false]])->normalize("آب\u{200C}میوه"));
        $this->assertSame("آب\u{200C}میوه", $this->normalizer(['search' => ['zwnj' => 'preserve', 'normalize_madda_alef' => false]])->normalize("آب\u{200C}میوه"));
        $this->assertSame('آبمیوه', $this->normalizer(['search' => ['zwnj' => 'remove', 'normalize_madda_alef' => false]])->normalize("آب\u{200C}میوه"));
    }

    public function test_it_removes_punctuation_for_search(): void
    {
        $this->assertSame('Hello دنیا', $this->normalizer()->normalize('Hello، دنیا!'));
    }

    public function test_it_collapses_digit_groups(): void
    {
        $this->assertSame('12500', $this->normalizer()->normalize('۱۲,۵۰۰'));
        $this->assertSame('12500', $this->normalizer()->normalize('۱۲ ۵۰۰'));
    }

    public function test_it_converts_persian_and_arabic_digits_to_english(): void
    {
        $this->assertSame('123456', $this->normalizer()->normalize('۱۲۳٤٥۶'));
    }

    public function test_madda_alef_can_be_normalized_or_preserved(): void
    {
        $this->assertSame('اب میوه', $this->normalizer()->normalize("آب\u{200C}میوه"));
        $this->assertSame('آب میوه', $this->normalizer(['search' => ['normalize_madda_alef' => false]])->normalize("آب\u{200C}میوه"));
    }

    public function test_expected_search_examples(): void
    {
        $normalizer = $this->normalizer();

        $this->assertSame('کیک شکلاتی', $normalizer->normalize("كیك\u{0650} شکلاتي"));
        $this->assertSame('اب میوه سن ایچ 12500 تومان', $normalizer->normalize("آب\u{200C}میوه سن\u{200C}ایچ ۱۲,۵۰۰ تومان!"));
        $this->assertSame('کتاب های خوب', $normalizer->normalize("  کتاب\u{200C}های   خوب  "));
        $this->assertSame('12500', $normalizer->normalize('۱۲ ۵۰۰'));
        $this->assertSame('Hello دنیا', $normalizer->normalize('Hello، دنیا!'));
    }

    public function test_it_is_idempotent(): void
    {
        $normalizer = $this->normalizer();
        $normalized = $normalizer->normalize("آب\u{200C}میوه سن\u{200C}ایچ ۱۲,۵۰۰ تومان!");

        $this->assertSame($normalized, $normalizer->normalize($normalized));
    }

    public function test_it_returns_empty_string_for_null(): void
    {
        $this->assertSame('', $this->normalizer()->normalize(null));
    }

    public function test_it_returns_tokens(): void
    {
        $this->assertSame(
            ['اب', 'میوه', 'سن', 'ایچ'],
            $this->normalizer()->tokens("آب\u{200C}میوه سن\u{200C}ایچ"),
        );
    }

    /** @param array<string, mixed> $options */
    private function normalizer(array $options = []): PersianSearchNormalizer
    {
        return new PersianSearchNormalizer(new PersianNumberNormalizer, $options);
    }
}
