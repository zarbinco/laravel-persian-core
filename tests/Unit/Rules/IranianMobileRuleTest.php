<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\IranianMobile;
use Zarbinco\PersianCore\Tests\TestCase;

class IranianMobileRuleTest extends TestCase
{
    public function test_passes_for_national_mobile(): void
    {
        $this->assertTrue($this->passes('09121234567'));
    }

    public function test_passes_for_persian_digits(): void
    {
        $this->assertTrue($this->passes('۰۹۱۲۱۲۳۴۵۶۷'));
    }

    public function test_passes_for_plus_98(): void
    {
        $this->assertTrue($this->passes('+989121234567'));
    }

    public function test_passes_for_0098(): void
    {
        $this->assertTrue($this->passes('00989121234567'));
    }

    public function test_passes_for_98(): void
    {
        $this->assertTrue($this->passes('989121234567'));
    }

    public function test_passes_for_missing_leading_zero(): void
    {
        $this->assertTrue($this->passes('9121234567'));
    }

    public function test_fails_for_landline_shape(): void
    {
        $this->assertFalse($this->passes('02112345678'));
    }

    public function test_fails_for_short_value(): void
    {
        $this->assertFalse($this->passes('123'));
    }

    public function test_fails_for_letters(): void
    {
        $this->assertFalse($this->passes('abc'));
    }

    public function test_passes_for_null(): void
    {
        $this->assertTrue($this->passes(null));
    }

    public function test_package_translation_is_loaded(): void
    {
        $this->assertNotSame(
            'persian-core::validation.iranian_mobile',
            __('persian-core::validation.iranian_mobile'),
        );
    }

    private function passes(mixed $value): bool
    {
        return Validator::make(['mobile' => $value], ['mobile' => [new IranianMobile]])->passes();
    }
}
