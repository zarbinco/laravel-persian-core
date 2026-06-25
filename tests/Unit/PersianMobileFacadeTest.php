<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianMobileFacadeTest extends TestCase
{
    public function test_facade_mobile_works(): void
    {
        $this->assertSame('09121234567', Persian::mobile('۰۹۱۲ ۱۲۳ ۴۵۶۷')->national());
    }
}
