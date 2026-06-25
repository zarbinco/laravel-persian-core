<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\IranianSheba;
use Zarbinco\PersianCore\Tests\TestCase;

class IranianShebaRuleTest extends TestCase
{
    public function test_passes_for_valid_sheba(): void
    {
        $this->assertTrue($this->passes('IR180100000000000000000000'));
    }

    public function test_passes_for_formatted_spaces(): void
    {
        $this->assertTrue($this->passes('IR18 0100 0000 0000 0000 0000 00'));
    }

    public function test_fails_for_incomplete_formatted_sheba(): void
    {
        $this->assertFalse($this->passes('IR18 0100 0000 0000 0000 0000'));
    }

    public function test_fails_for_invalid_checksum(): void
    {
        $this->assertFalse($this->passes('IR000100000000000000000000'));
    }

    public function test_fails_for_short_value(): void
    {
        $this->assertFalse($this->passes('123'));
    }

    public function test_passes_for_persian_digits(): void
    {
        $this->assertTrue($this->passes('IR۱۸۰۱۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰'));
    }

    public function test_passes_for_null(): void
    {
        $this->assertTrue($this->passes(null));
    }

    private function passes(mixed $value): bool
    {
        return Validator::make(['sheba' => $value], ['sheba' => [new IranianSheba]])->passes();
    }
}
