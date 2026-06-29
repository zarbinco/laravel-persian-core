<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Support\IranianBank;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianCardFacadeTest extends TestCase
{
    public function test_card_bank_returns_iranian_bank(): void
    {
        $this->assertInstanceOf(IranianBank::class, Persian::card('6037991234567890')->bank());
    }

    public function test_card_bank_slug_returns_expected_slug(): void
    {
        $this->assertSame('melli', Persian::card('6037991234567890')->bankSlug());
    }

    public function test_card_bank_name_returns_expected_english_name(): void
    {
        $this->assertSame('Bank Melli Iran', Persian::card('6037991234567890')->bankName());
    }

    public function test_card_bank_name_fa_returns_expected_persian_name(): void
    {
        $this->assertSame('بانک ملی ایران', Persian::card('6037991234567890')->bankNameFa());
    }

    public function test_bank_from_card_returns_same_bank_object_data(): void
    {
        $this->assertSame(
            Persian::card('6037991234567890')->bank()?->toArray(),
            Persian::bankFromCard('6037991234567890')?->toArray(),
        );
    }

    public function test_unknown_card_returns_null(): void
    {
        $this->assertNull(Persian::card('1111111234567890')->bank());
        $this->assertNull(Persian::bankFromCard('1111111234567890'));
    }
}
