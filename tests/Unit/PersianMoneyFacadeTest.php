<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianMoneyFacadeTest extends TestCase
{
    public function test_facade_money_formats_default_toman(): void
    {
        $this->assertSame('۱,۲۵۰,۰۰۰ تومان', Persian::money(1250000)->format());
    }
}
