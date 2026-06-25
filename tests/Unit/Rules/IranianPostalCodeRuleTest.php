<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\IranianPostalCode;
use Zarbinco\PersianCore\Tests\TestCase;

class IranianPostalCodeRuleTest extends TestCase
{
    public function test_passes_for_postal_code(): void
    {
        $this->assertTrue($this->passes('1234567890'));
    }

    public function test_passes_for_persian_digits(): void
    {
        $this->assertTrue($this->passes('۱۲۳۴۵۶۷۸۹۰'));
    }

    public function test_fails_for_short_value(): void
    {
        $this->assertFalse($this->passes('123'));
    }

    public function test_fails_for_letters(): void
    {
        $this->assertFalse($this->passes('abc'));
    }

    public function test_fails_for_repeated_digits_by_default(): void
    {
        $this->assertFalse($this->passes('1111111111'));
    }

    public function test_repeated_digits_can_be_allowed_by_config(): void
    {
        config(['persian-core.validation.iranian_postal_code.reject_repeated_digits' => false]);

        $this->assertTrue($this->passes('1111111111'));
    }

    public function test_passes_for_null(): void
    {
        $this->assertTrue($this->passes(null));
    }

    private function passes(mixed $value): bool
    {
        return Validator::make(['postal_code' => $value], ['postal_code' => [new IranianPostalCode]])->passes();
    }
}
