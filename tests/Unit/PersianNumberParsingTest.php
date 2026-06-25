<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianNumberParsingTest extends TestCase
{
    public function test_clean_removes_persian_comma_thousands(): void
    {
        $this->assertSame('1250000', Persian::number('۱,۲۵۰,۰۰۰')->clean());
    }

    public function test_clean_removes_arabic_thousands_separator(): void
    {
        $this->assertSame('1250000', Persian::number('۱٬۲۵۰٬۰۰۰')->clean());
    }

    public function test_clean_normalizes_decimal_separator(): void
    {
        $this->assertSame('12.50', Persian::number('۱۲٫۵۰')->clean());
    }

    public function test_clean_normalizes_unicode_minus(): void
    {
        $this->assertSame('-123', Persian::number('−۱۲۳')->clean());
    }

    public function test_digits_only_extracts_digits(): void
    {
        $this->assertSame('123', Persian::number('abc ۱۲۳ def')->digitsOnly());
    }

    public function test_to_int(): void
    {
        $this->assertSame(123, Persian::number('۱۲۳')->toInt());
    }

    public function test_to_float(): void
    {
        $this->assertSame(12.5, Persian::number('۱۲٫۵')->toFloat());
    }

    public function test_is_numeric_true(): void
    {
        $this->assertTrue(Persian::number('۱۲۳۴۵۶۷')->isNumeric());
    }

    public function test_is_numeric_false(): void
    {
        $this->assertFalse(Persian::number('abc')->isNumeric());
    }

    public function test_format_default_uses_persian_display_digits(): void
    {
        $this->assertSame('۱,۲۳۴,۵۶۷', Persian::number('۱۲۳۴۵۶۷')->format());
    }

    public function test_format_english_digits(): void
    {
        $this->assertSame('1,234,567', Persian::number('۱۲۳۴۵۶۷')->format('en'));
    }

    public function test_format_preserves_decimal(): void
    {
        $this->assertSame('1,234,567.50', Persian::number('۱۲۳۴۵۶۷٫۵۰')->format('en'));
    }

    public function test_format_preserves_negative(): void
    {
        $this->assertSame('-1,234,567', Persian::number('−۱۲۳۴۵۶۷')->format('en'));
    }

    public function test_null_number_input_cleans_to_empty_string(): void
    {
        $this->assertSame('', Persian::number(null)->clean());
    }
}
