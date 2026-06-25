<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\PersianAlpha;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianAlphaRuleTest extends TestCase
{
    public function test_passes_for_persian_letters(): void
    {
        $this->assertTrue($this->passes('علی کاظمی'));
    }

    public function test_passes_for_zwnj(): void
    {
        $this->assertTrue($this->passes("می\u{200C}روم"));
    }

    public function test_fails_for_digits(): void
    {
        $this->assertFalse($this->passes('علی123'));
    }

    public function test_fails_for_english_letters(): void
    {
        $this->assertFalse($this->passes('Ali'));
    }

    public function test_fails_for_punctuation(): void
    {
        $this->assertFalse($this->passes('علی!'));
    }

    public function test_passes_for_null(): void
    {
        $this->assertTrue($this->passes(null));
    }

    private function passes(mixed $value): bool
    {
        return Validator::make(['name' => $value], ['name' => [new PersianAlpha]])->passes();
    }
}
