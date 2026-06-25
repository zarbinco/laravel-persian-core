<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\IranianCardNumber;
use Zarbinco\PersianCore\Tests\TestCase;

class IranianCardNumberRuleTest extends TestCase
{
    public function test_passes_for_valid_luhn_card(): void
    {
        $this->assertTrue($this->passes('6037990000000006'));
    }

    public function test_passes_for_persian_digits(): void
    {
        $this->assertTrue($this->passes('۶۰۳۷۹۹۰۰۰۰۰۰۰۰۰۶'));
    }

    public function test_fails_for_invalid_luhn(): void
    {
        $this->assertFalse($this->passes('6037990000000000'));
    }

    public function test_fails_for_short_value(): void
    {
        $this->assertFalse($this->passes('123'));
    }

    public function test_passes_for_null(): void
    {
        $this->assertTrue($this->passes(null));
    }

    public function test_luhn_can_be_disabled_by_config(): void
    {
        config(['persian-core.validation.iranian_card_number.require_luhn' => false]);

        $this->assertTrue($this->passes('6037990000000000'));
    }

    public function test_iranian_bin_can_be_required_by_config(): void
    {
        config(['persian-core.validation.iranian_card_number.require_iranian_bin' => true]);

        $this->assertFalse($this->passes('4111111111111111'));
    }

    private function passes(mixed $value): bool
    {
        return Validator::make(['card' => $value], ['card' => [new IranianCardNumber]])->passes();
    }
}
