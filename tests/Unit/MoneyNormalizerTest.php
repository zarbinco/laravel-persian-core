<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Tests\TestCase;

class MoneyNormalizerTest extends TestCase
{
    public function test_money_clean_with_toman_label(): void
    {
        $this->assertSame('1250000', Persian::money('۱,۲۵۰,۰۰۰ تومان')->clean());
    }

    public function test_money_clean_with_rial_label(): void
    {
        $this->assertSame('12500000', Persian::money('۱۲,۵۰۰,۰۰۰ ریال')->clean());
    }

    public function test_money_clean_with_arabic_rial_spelling(): void
    {
        $this->assertSame('12500000', Persian::money('۱۲,۵۰۰,۰۰۰ ريال')->clean());
    }

    public function test_detected_currency_toman(): void
    {
        $this->assertSame('toman', Persian::money('۱,۲۵۰,۰۰۰ تومان')->detectedCurrency());
    }

    public function test_detected_currency_toman_informal(): void
    {
        $this->assertSame('toman', Persian::money('۱,۲۵۰,۰۰۰ تومن')->detectedCurrency());
    }

    public function test_detected_currency_rial(): void
    {
        $this->assertSame('rial', Persian::money('۱۲,۵۰۰,۰۰۰ ریال')->detectedCurrency());
    }

    public function test_detected_currency_null(): void
    {
        $this->assertNull(Persian::money('۱۲,۵۰۰,۰۰۰')->detectedCurrency());
    }

    public function test_value_returns_int(): void
    {
        $this->assertSame(1250000, Persian::money('۱,۲۵۰,۰۰۰ تومان')->value());
    }

    public function test_null_value(): void
    {
        $this->assertNull(Persian::money(null)->value());
    }
}
