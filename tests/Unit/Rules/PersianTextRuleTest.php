<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\PersianText;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianTextRuleTest extends TestCase
{
    public function test_passes_for_persian_text(): void
    {
        $this->assertTrue($this->passes('علی کاظمی'));
    }

    public function test_passes_for_persian_text_with_digits_and_punctuation(): void
    {
        $this->assertTrue($this->passes('سلام، علی ۱۲۳!'));
    }

    public function test_fails_for_english_text(): void
    {
        $this->assertFalse($this->passes('John Smith'));
    }

    public function test_fails_for_digits_only(): void
    {
        $this->assertFalse($this->passes('123456'));
    }

    public function test_passes_for_null(): void
    {
        $this->assertTrue($this->passes(null));
    }

    private function passes(mixed $value): bool
    {
        return Validator::make(['name' => $value], ['name' => [new PersianText]])->passes();
    }
}
