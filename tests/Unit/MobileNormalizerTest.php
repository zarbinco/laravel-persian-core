<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Tests\TestCase;

class MobileNormalizerTest extends TestCase
{
    public function test_persian_mobile_with_spaces_normalizes(): void
    {
        $this->assertSame('09121234567', Persian::mobile('۰۹۱۲ ۱۲۳ ۴۵۶۷')->normalize());
    }

    public function test_plus_98_to_national(): void
    {
        $this->assertSame('09121234567', Persian::mobile('+989121234567')->national());
    }

    public function test_0098_to_national(): void
    {
        $this->assertSame('09121234567', Persian::mobile('00989121234567')->national());
    }

    public function test_98_to_national(): void
    {
        $this->assertSame('09121234567', Persian::mobile('989121234567')->national());
    }

    public function test_missing_leading_zero_to_national(): void
    {
        $this->assertSame('09121234567', Persian::mobile('9121234567')->national());
    }

    public function test_national_to_international(): void
    {
        $this->assertSame('+989121234567', Persian::mobile('09121234567')->international());
    }

    public function test_e164_alias(): void
    {
        $this->assertSame('+989121234567', Persian::mobile('09121234567')->e164());
    }

    public function test_mask(): void
    {
        $this->assertSame('0912***4567', Persian::mobile('09121234567')->mask());
    }

    public function test_null_mobile_input_normalizes_to_empty_string(): void
    {
        $this->assertSame('', Persian::mobile(null)->normalize());
    }
}
