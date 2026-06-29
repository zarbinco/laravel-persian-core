<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\IranianCardNumber;
use Zarbinco\PersianCore\Rules\IranianMobile;
use Zarbinco\PersianCore\Rules\IranianNationalCode;
use Zarbinco\PersianCore\Rules\IranianPostalCode;
use Zarbinco\PersianCore\Rules\IranianSheba;
use Zarbinco\PersianCore\Rules\PersianMoneyAmount;
use Zarbinco\PersianCore\Tests\TestCase;

class StrictValidationModeTest extends TestCase
{
    public function test_strict_validation_is_enabled_by_default(): void
    {
        $this->assertTrue((bool) config('persian-core.validation.strict'));
    }

    public function test_strict_mode_rejects_embedded_garbage(): void
    {
        $this->assertFalse($this->passes('value', 'abc09121234567def', new IranianMobile));
        $this->assertFalse($this->passes('value', 'abc0123456789def', new IranianNationalCode));
        $this->assertFalse($this->passes('value', 'abc1234567890def', new IranianPostalCode));
        $this->assertFalse($this->passes('value', 'abc6037990000000006def', new IranianCardNumber));
        $this->assertFalse($this->passes('value', 'abcIR180100000000000000000000def', new IranianSheba));
        $this->assertFalse($this->passes('value', 'abc125000def', new PersianMoneyAmount));
        $this->assertFalse($this->passes('value', 'abc ۱۲۳ تومان def', new PersianMoneyAmount));
    }

    public function test_strict_mode_accepts_arabic_digits(): void
    {
        $this->assertTrue($this->passes('value', '٠٩١٢١٢٣٤٥٦٧', new IranianMobile));
        $this->assertTrue($this->passes('value', '٠١٢٣٤٥٦٧٨٩', new IranianNationalCode));
        $this->assertTrue($this->passes('value', '١٢٣٤٥٦٧٨٩٠', new IranianPostalCode));
        $this->assertTrue($this->passes('value', '٦٠٣٧٩٩٠٠٠٠٠٠٠٠٠٦', new IranianCardNumber));
        $this->assertTrue($this->passes('value', 'IR١٨٠١٠٠٠٠٠٠٠٠٠٠٠٠٠٠٠٠٠٠٠٠', new IranianSheba));
        $this->assertTrue($this->passes('value', '١,٢٥٠,٠٠٠ تومان', new PersianMoneyAmount));
    }

    public function test_strict_mode_accepts_reasonable_separators(): void
    {
        $this->assertTrue($this->passes('value', '0912-123-4567', new IranianMobile));
        $this->assertTrue($this->passes('value', '(0912) 123 4567', new IranianMobile));
        $this->assertTrue($this->passes('value', '012-345-6789', new IranianNationalCode));
        $this->assertTrue($this->passes('value', '12345-67890', new IranianPostalCode));
        $this->assertTrue($this->passes('value', '6037-9900-0000-0006', new IranianCardNumber));
        $this->assertTrue($this->passes('value', 'IR18-0100-0000-0000-0000-0000-00', new IranianSheba));
        $this->assertTrue($this->passes('value', '۱,۲۵۰,۰۰۰ تومان', new PersianMoneyAmount));
    }

    public function test_relaxed_mode_can_be_enabled_by_config(): void
    {
        config(['persian-core.validation.strict' => false]);

        $this->assertTrue($this->passes('value', 'abc09121234567def', new IranianMobile));
        $this->assertTrue($this->passes('value', 'abc0123456789def', new IranianNationalCode));
        $this->assertTrue($this->passes('value', 'abc1234567890def', new IranianPostalCode));
        $this->assertTrue($this->passes('value', 'abc6037990000000006def', new IranianCardNumber));
        $this->assertTrue($this->passes('value', 'abc125000def', new PersianMoneyAmount));
    }

    public function test_relaxed_mode_can_be_enabled_by_constructor(): void
    {
        $this->assertTrue($this->passes('value', 'abc09121234567def', new IranianMobile(strict: false)));
        $this->assertTrue($this->passes('value', 'abc0123456789def', new IranianNationalCode(strict: false)));
        $this->assertTrue($this->passes('value', 'abc1234567890def', new IranianPostalCode(strict: false)));
        $this->assertTrue($this->passes('value', 'abc6037990000000006def', new IranianCardNumber(strict: false)));
        $this->assertTrue($this->passes('value', 'IR180100000000000000000000', new IranianSheba(strict: false)));
        $this->assertTrue($this->passes('value', 'abc125000def', new PersianMoneyAmount(strict: false)));
    }

    public function test_constructor_strict_mode_overrides_relaxed_config(): void
    {
        config(['persian-core.validation.strict' => false]);

        $this->assertFalse($this->passes('value', 'abc09121234567def', new IranianMobile(strict: true)));
        $this->assertFalse($this->passes('value', 'abc0123456789def', new IranianNationalCode(strict: true)));
        $this->assertFalse($this->passes('value', 'abc1234567890def', new IranianPostalCode(strict: true)));
        $this->assertFalse($this->passes('value', 'abc6037990000000006def', new IranianCardNumber(strict: true)));
        $this->assertFalse($this->passes('value', 'abcIR180100000000000000000000def', new IranianSheba(strict: true)));
        $this->assertFalse($this->passes('value', 'abc125000def', new PersianMoneyAmount(strict: true)));
    }

    public function test_empty_value_behavior_respects_config(): void
    {
        $this->assertTrue($this->passes('value', '', new IranianMobile));
        $this->assertTrue($this->passes('value', '', new IranianNationalCode));
        $this->assertTrue($this->passes('value', '', new IranianPostalCode));
        $this->assertTrue($this->passes('value', '', new IranianCardNumber));
        $this->assertTrue($this->passes('value', '', new IranianSheba));
        $this->assertTrue($this->passes('value', '', new PersianMoneyAmount));
        $this->assertTrue($this->passes('value', null, new IranianMobile));

        config(['persian-core.validation.empty_values_pass' => false]);

        $this->assertFalse($this->passes('value', '', new IranianMobile));
        $this->assertFalse($this->passes('value', '', new IranianNationalCode));
        $this->assertFalse($this->passes('value', '', new IranianPostalCode));
        $this->assertFalse($this->passes('value', '', new IranianCardNumber));
        $this->assertFalse($this->passes('value', '', new IranianSheba));
        $this->assertFalse($this->passes('value', '', new PersianMoneyAmount));
        $this->assertFalse($this->passes('value', null, new IranianMobile));
    }

    private function passes(string $attribute, mixed $value, ValidationRule $rule): bool
    {
        return Validator::make([$attribute => $value], [$attribute => [$rule]])->passes();
    }
}
