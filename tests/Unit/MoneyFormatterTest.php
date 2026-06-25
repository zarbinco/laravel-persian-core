<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Formatters\MoneyFormatter;
use Zarbinco\PersianCore\PersianManager;
use Zarbinco\PersianCore\Tests\TestCase;

class MoneyFormatterTest extends TestCase
{
    public function test_default_format(): void
    {
        $this->assertSame('۱,۲۵۰,۰۰۰ تومان', Persian::money(1250000)->format());
    }

    public function test_toman_format(): void
    {
        $this->assertSame('۱,۲۵۰,۰۰۰ تومان', Persian::money(1250000)->toman());
    }

    public function test_rial_format(): void
    {
        $this->assertSame('۱۲,۵۰۰,۰۰۰ ریال', Persian::money(12500000)->rial());
    }

    public function test_english_format(): void
    {
        $this->assertSame('1,250,000 toman', Persian::money(1250000)->format('toman', 'en'));
    }

    public function test_negative_format(): void
    {
        $this->assertSame('-1,250,000 toman', Persian::money(-1250000)->format('toman', 'en'));
    }

    public function test_from_rial_to_toman(): void
    {
        $this->assertSame(1250000, Persian::money(12500000)->fromRial()->toToman());
    }

    public function test_from_toman_to_rial(): void
    {
        $this->assertSame(12500000, Persian::money(1250000)->fromToman()->toRial());
    }

    public function test_detected_currency_conversion_from_rial_to_toman(): void
    {
        $this->assertSame(1250000, Persian::money('۱۲,۵۰۰,۰۰۰ ریال')->toToman());
    }

    public function test_detected_currency_conversion_from_toman_to_rial(): void
    {
        $this->assertSame(12500000, Persian::money('۱,۲۵۰,۰۰۰ تومان')->toRial());
    }

    public function test_format_toman_from_rial(): void
    {
        $this->assertSame('۱,۲۵۰,۰۰۰ تومان', Persian::money(12500000)->fromRial()->formatToman());
    }

    public function test_format_rial_from_toman(): void
    {
        $this->assertSame('۱۲,۵۰۰,۰۰۰ ریال', Persian::money(1250000)->fromToman()->formatRial());
    }

    public function test_null_format(): void
    {
        $this->assertSame('', Persian::money(null)->format());
    }

    public function test_to_string_returns_default_format(): void
    {
        $this->assertSame('۱,۲۵۰,۰۰۰ تومان', (string) Persian::money(1250000));
    }

    public function test_config_default_currency_rial(): void
    {
        config(['persian-core.money.default_currency' => 'rial']);
        $this->refreshPersianFacade();

        $this->assertSame('۱,۲۵۰,۰۰۰ ریال', Persian::money(1250000)->format());
    }

    public function test_invalid_currency_fallback(): void
    {
        $this->assertSame('۱,۲۵۰,۰۰۰ تومان', Persian::money(1250000)->format('invalid'));
    }

    public function test_invalid_digits_fallback(): void
    {
        $this->assertSame('۱,۲۵۰,۰۰۰ تومان', Persian::money(1250000)->format('toman', 'invalid'));
    }

    private function refreshPersianFacade(): void
    {
        $this->app->forgetInstance(MoneyFormatter::class);
        $this->app->forgetInstance(PersianManager::class);
        Persian::clearResolvedInstance('persian-core');
    }
}
