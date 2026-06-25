<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\PersianMoneyAmount;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianMoneyAmountRuleTest extends TestCase
{
    public function test_passes_for_toman_amount(): void
    {
        $this->assertTrue($this->passes('۱,۲۵۰,۰۰۰ تومان'));
    }

    public function test_passes_for_plain_amount(): void
    {
        $this->assertTrue($this->passes('1250000'));
    }

    public function test_passes_for_rial_amount(): void
    {
        $this->assertTrue($this->passes('۱۲,۵۰۰,۰۰۰ ریال'));
    }

    public function test_fails_for_letters(): void
    {
        $this->assertFalse($this->passes('abc'));
    }

    public function test_fails_for_label_only(): void
    {
        $this->assertFalse($this->passes('تومان'));
    }

    public function test_passes_for_null(): void
    {
        $this->assertTrue($this->passes(null));
    }

    private function passes(mixed $value): bool
    {
        return Validator::make(['amount' => $value], ['amount' => [new PersianMoneyAmount]])->passes();
    }
}
