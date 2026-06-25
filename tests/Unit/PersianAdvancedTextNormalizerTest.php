<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianAdvancedTextNormalizerTest extends TestCase
{
    public function test_for_storage_is_same_as_normalize_for_text(): void
    {
        $normalizer = new PersianTextNormalizer;

        $this->assertSame('علی کاظمی', $normalizer->forStorage('علي كاظمي'));
        $this->assertSame($normalizer->normalize('علي كاظمي'), $normalizer->forStorage('علي كاظمي'));
    }

    public function test_for_display_normalizes_punctuation_spacing(): void
    {
        $this->assertSame(
            'سلام، علی! خوبی؟',
            Persian::text('سلام ،  علی  ! خوبی ؟')->forDisplay(),
        );
    }

    public function test_for_display_normalizes_ellipsis(): void
    {
        $this->assertSame('سلام…', Persian::text('سلام...')->forDisplay());
    }

    public function test_for_display_does_not_break_semantic_version_strings(): void
    {
        $this->assertSame(
            'نسخه 1.2.3 منتشر شد.',
            Persian::text('نسخه 1.2.3 منتشر شد.')->forDisplay(),
        );
    }

    public function test_for_display_does_not_break_emails(): void
    {
        $this->assertSame(
            'ایمیل test@example.com درست است.',
            Persian::text('ایمیل test@example.com درست است.')->forDisplay(),
        );
    }

    public function test_normalize_preserves_zwnj(): void
    {
        $this->assertSame("می\u{200C}روم", Persian::text("می\u{200C}روم")->normalize());
    }

    public function test_for_search_converts_zwnj_to_space_by_default(): void
    {
        $this->assertSame('می روم', Persian::text("می\u{200C}روم")->forSearch());
    }

    public function test_for_search_removes_punctuation(): void
    {
        $this->assertSame('سلام علی خوبی', Persian::text('سلام، علی! خوبی؟')->forSearch());
    }

    public function test_for_search_normalizes_arabic_alef_variants(): void
    {
        $this->assertSame('ایمان امان ادم', Persian::text('إيمان أمان ٱدم')->forSearch());
    }

    public function test_for_search_normalizes_teh_marbuta(): void
    {
        $this->assertSame('مدرسه', Persian::text('مدرسة')->forSearch());
    }

    public function test_pipeline_for_search_converts_text_and_numbers(): void
    {
        $this->assertSame(
            'علی کاظمی مبلغ 1250000 تومان',
            Persian::normalize('علي كاظمي مبلغ ۱,۲۵۰,۰۰۰ تومان')->forSearch(),
        );
    }

    public function test_search_zwnj_config_preserve(): void
    {
        $normalizer = new PersianTextNormalizer([
            'search' => [
                'zwnj' => 'preserve',
            ],
        ]);

        $this->assertSame("می\u{200C}روم", $normalizer->forSearch("می\u{200C}روم"));
    }

    public function test_search_zwnj_config_remove(): void
    {
        $normalizer = new PersianTextNormalizer([
            'search' => [
                'zwnj' => 'remove',
            ],
        ]);

        $this->assertSame('میروم', $normalizer->forSearch("می\u{200C}روم"));
    }

    public function test_invalid_search_zwnj_config_falls_back_to_space(): void
    {
        $normalizer = new PersianTextNormalizer([
            'search' => [
                'zwnj' => 'invalid',
            ],
        ]);

        $this->assertSame('می روم', $normalizer->forSearch("می\u{200C}روم"));
    }

    public function test_facade_searchable_aliases_pipeline_for_search(): void
    {
        $this->assertSame('علی 123', Persian::searchable('علي ۱۲۳'));
    }
}
