<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Support\IranianBank;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianShebaFacadeTest extends TestCase
{
    public function test_sheba_bank_returns_iranian_bank(): void
    {
        $this->assertInstanceOf(IranianBank::class, Persian::sheba('IR170170000000000000000000')->bank());
    }

    public function test_sheba_bank_slug_returns_expected_slug(): void
    {
        $this->assertSame('melli', Persian::sheba('IR170170000000000000000000')->bankSlug());
    }

    public function test_sheba_bank_name_returns_expected_english_name(): void
    {
        $this->assertSame('Bank Melli Iran', Persian::sheba('IR170170000000000000000000')->bankName());
    }

    public function test_sheba_bank_name_fa_returns_expected_persian_name(): void
    {
        $this->assertSame('بانک ملی ایران', Persian::sheba('IR170170000000000000000000')->bankNameFa());
    }

    public function test_bank_from_sheba_returns_same_bank_object_data(): void
    {
        $this->assertSame(
            Persian::sheba('IR170170000000000000000000')->bank()?->toArray(),
            Persian::bankFromSheba('IR170170000000000000000000')?->toArray(),
        );
    }

    public function test_unknown_sheba_returns_null(): void
    {
        $this->assertNull(Persian::sheba('IR179990000000000000000000')->bank());
        $this->assertNull(Persian::bankFromSheba('IR179990000000000000000000'));
    }
}
