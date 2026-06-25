<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Rules\PersianAlphaNum;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianAlphaNumRuleTest extends TestCase
{
    public function test_passes_for_persian_letters_and_persian_digits(): void
    {
        $this->assertTrue($this->passes('علی ۱۲۳'));
    }

    public function test_passes_for_persian_letters_and_english_digits(): void
    {
        $this->assertTrue($this->passes('علی 123'));
    }

    public function test_fails_for_english_letters(): void
    {
        $this->assertFalse($this->passes('Ali 123'));
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
        return Validator::make(['name' => $value], ['name' => [new PersianAlphaNum]])->passes();
    }
}
