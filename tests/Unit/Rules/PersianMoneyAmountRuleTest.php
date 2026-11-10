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

    public function test_accepts_valid_whitespace_grouping(): void
    {
        $this->assertTrue($this->passes('1 000'));
        $this->assertTrue($this->passes('۱۲ ۵۰۰ تومان'));
        $this->assertTrue($this->passes('١ ٢٠٠ ٠٠٠ ریال'));
        $this->assertTrue($this->passes("۱۲\u{00A0}۵۰۰ تومان"));
        $this->assertTrue($this->passes("۱۲\t۵۰۰ تومان"));
        $this->assertFalse($this->passes('12 34'));
        $this->assertFalse($this->passes('1,000 000'));
    }

    public function test_arabic_thousands_separator_passes_strict_and_relaxed_validation(): void
    {
        foreach (['1٬000', '۱٬۲۵۰٬۰۰۰ ریال', '١٬٢٠٠٬٠٠٠ ريال'] as $value) {
            $this->assertTrue($this->passes($value, strict: true), 'Strict mode: '.$value);
            $this->assertTrue($this->passes($value, strict: false), 'Relaxed mode: '.$value);
        }

        foreach (['1٬000,000', '12٬34', '1٬00٬0'] as $value) {
            $this->assertFalse($this->passes($value, strict: true), 'Strict mode: '.$value);
            $this->assertFalse($this->passes($value, strict: false), 'Relaxed mode: '.$value);
        }
    }

    public function test_fails_for_fractional_toman_amount(): void
    {
        $this->assertFalse($this->passes('۱۲٫۵ تومان'));
        $this->assertFalse($this->passes('12.5 toman'));
    }

    public function test_passes_for_integer_equivalent_decimal_amount(): void
    {
        $this->assertTrue($this->passes('۱۲٫۰۰ تومان'));
        $this->assertTrue($this->passes('12.0 toman'));
    }

    public function test_fails_for_letters(): void
    {
        $this->assertFalse($this->passes('abc'));
    }

    public function test_fails_for_label_only(): void
    {
        $this->assertFalse($this->passes('تومان'));
    }

    public function test_rejects_sign_only_and_fractional_values_in_strict_and_relaxed_modes(): void
    {
        foreach (['-', '−', '- تومان', '٫۵', '-٫۵', '.5', '-.5'] as $value) {
            $this->assertFalse($this->passes($value), 'Strict mode: '.$value);
            $this->assertFalse($this->passes($value, strict: false), 'Relaxed mode: '.$value);
        }
    }

    public function test_relaxed_mode_extracts_one_amount_but_rejects_multiple_amounts(): void
    {
        foreach (['قیمت ۱۰۰ تومان', 'قیمت: ۱۰۰ تومان', 'Price: 1500 toman'] as $value) {
            $this->assertFalse($this->passes($value, strict: true), 'Strict mode: '.$value);
            $this->assertTrue($this->passes($value, strict: false), 'Relaxed mode: '.$value);
        }

        foreach (['۱۰۰ تومان ۲۰۰ ریال', '۱۰۰تومان۲۰۰ریال', '۱۰۰ تومان ۲۰۰'] as $value) {
            $this->assertFalse($this->passes($value, strict: true), 'Strict mode: '.$value);
            $this->assertFalse($this->passes($value, strict: false), 'Relaxed mode: '.$value);
        }
    }

    public function test_strict_and_relaxed_modes_reject_unconsumed_numeric_material(): void
    {
        $inputs = [
            '1.0 200',
            '12.00 34',
            '1,000.00 200',
            '12 -34',
            '12 +34',
            '100 تومان 200+',
            '100 تومان 20.0.5',
            '100 تومان 12.0.abc',
            "100\n200",
        ];

        foreach ($inputs as $value) {
            $this->assertFalse($this->passes($value, strict: true), 'Strict mode: '.$value);
            $this->assertFalse($this->passes($value, strict: false), 'Relaxed mode: '.$value);
        }
    }

    public function test_supported_digit_scripts_are_accepted_and_unsupported_scripts_rejected_in_both_modes(): void
    {
        foreach (['۱۲۳', '١٢٣', '123', '۱۲ ۵۰۰ تومان', '۱,۲۵۰,۰۰۰ ریال', '12.00', '-۱۲۵ تومان'] as $value) {
            $this->assertTrue($this->passes($value, strict: true), 'Strict mode: '.$value);
            $this->assertTrue($this->passes($value, strict: false), 'Relaxed mode: '.$value);
        }

        foreach (['१२३', '１２３', '𝟙𝟚𝟛', '200१२३', 'قیمت १२۳ تومان'] as $value) {
            $this->assertFalse($this->passes($value, strict: true), 'Strict mode: '.$value);
            $this->assertFalse($this->passes($value, strict: false), 'Relaxed mode: '.$value);
        }
    }

    public function test_passes_for_null(): void
    {
        $this->assertTrue($this->passes(null));
    }

    private function passes(mixed $value, ?bool $strict = null): bool
    {
        return Validator::make(['amount' => $value], ['amount' => [new PersianMoneyAmount(strict: $strict)]])->passes();
    }
}
