<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianFacadeTest extends TestCase
{
    public function test_facade_clean_normalizes_text_and_digits_for_storage(): void
    {
        $this->assertSame('علی 123', Persian::clean('علي ۱۲۳'));
    }

    public function test_facade_clean_returns_empty_string_for_null(): void
    {
        $this->assertSame('', Persian::clean(null));
    }
}
