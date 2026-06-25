<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\IranianNationalCode;
use Zarbinco\PersianCore\Tests\TestCase;

class IranianNationalCodeRuleTest extends TestCase
{
    public function test_passes_for_valid_national_code(): void
    {
        $this->assertTrue($this->passes('0123456789'));
    }

    public function test_passes_for_persian_digits(): void
    {
        $this->assertTrue($this->passes('۰۱۲۳۴۵۶۷۸۹'));
    }

    public function test_fails_for_invalid_checksum(): void
    {
        $this->assertFalse($this->passes('0123456780'));
    }

    public function test_fails_for_repeated_digits(): void
    {
        $this->assertFalse($this->passes('1111111111'));
    }

    public function test_fails_for_short_value(): void
    {
        $this->assertFalse($this->passes('123'));
    }

    public function test_passes_for_null(): void
    {
        $this->assertTrue($this->passes(null));
    }

    private function passes(mixed $value): bool
    {
        return Validator::make(['code' => $value], ['code' => [new IranianNationalCode]])->passes();
    }
}
