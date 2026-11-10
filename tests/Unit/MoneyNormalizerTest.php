<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Tests\TestCase;

class MoneyNormalizerTest extends TestCase
{
    public function test_money_clean_with_toman_label(): void
    {
        $this->assertSame('1250000', Persian::money('۱,۲۵۰,۰۰۰ تومان')->clean());
    }

    public function test_money_clean_with_rial_label(): void
    {
        $this->assertSame('12500000', Persian::money('۱۲,۵۰۰,۰۰۰ ریال')->clean());
    }

    public function test_money_clean_with_arabic_rial_spelling(): void
    {
        $this->assertSame('12500000', Persian::money('۱۲,۵۰۰,۰۰۰ ريال')->clean());
    }

    public function test_arabic_thousands_separator_is_parsed_across_supported_digit_scripts(): void
    {
        $cases = [
            ['1٬000', '1000', 1000, null, '۱,۰۰۰ تومان'],
            ['۱٬۲۵۰٬۰۰۰ ریال', '1250000', 1250000, 'rial', '۱,۲۵۰,۰۰۰ ریال'],
            ['١٬٢٠٠٬٠٠٠ ريال', '1200000', 1200000, 'rial', '۱,۲۰۰,۰۰۰ ریال'],
        ];

        foreach ($cases as [$input, $clean, $value, $currency, $formatted]) {
            $money = Persian::money($input);

            $this->assertSame($clean, $money->clean(), $input);
            $this->assertSame($value, $money->value(), $input);
            $this->assertSame($formatted, $money->format(), $input);
            $this->assertSame($currency, $money->detectedCurrency(), $input);
        }
    }

    public function test_arabic_thousands_separator_rejects_mixed_or_malformed_grouping(): void
    {
        foreach (['1٬000,000', '12٬34', '1٬00٬0'] as $input) {
            $money = Persian::money($input);

            $this->assertSame('', $money->clean(), $input);
            $this->assertNull($money->value(), $input);
            $this->assertSame('', $money->format(), $input);
            $this->assertNull($money->detectedCurrency(), $input);
        }
    }

    public function test_detected_currency_toman(): void
    {
        $this->assertSame('toman', Persian::money('۱,۲۵۰,۰۰۰ تومان')->detectedCurrency());
    }

    public function test_detected_currency_toman_informal(): void
    {
        $this->assertSame('toman', Persian::money('۱,۲۵۰,۰۰۰ تومن')->detectedCurrency());
    }

    public function test_detected_currency_rial(): void
    {
        $this->assertSame('rial', Persian::money('۱۲,۵۰۰,۰۰۰ ریال')->detectedCurrency());
    }

    public function test_detected_currency_null(): void
    {
        $this->assertNull(Persian::money('۱۲,۵۰۰,۰۰۰')->detectedCurrency());
    }

    public function test_value_returns_int(): void
    {
        $this->assertSame(1250000, Persian::money('۱,۲۵۰,۰۰۰ تومان')->value());
    }

    public function test_fractional_values_are_rejected_instead_of_being_truncated(): void
    {
        $this->assertSame('', Persian::money('۱۲٫۵ تومان')->clean());
        $this->assertNull(Persian::money('۱۲٫۵ تومان')->value());
        $this->assertSame('', Persian::money('12.5 toman')->clean());
        $this->assertNull(Persian::money('12.5 toman')->value());
        $this->assertSame('', Persian::money('.5')->clean());
        $this->assertSame('', Persian::money('-.5')->clean());
        $this->assertSame('', Persian::money('٫۵')->clean());
        $this->assertSame('', Persian::money('-٫۵')->clean());
    }

    public function test_integer_equivalent_decimal_values_are_accepted(): void
    {
        $this->assertSame('12', Persian::money('۱۲٫۰۰ تومان')->clean());
        $this->assertSame(12, Persian::money('12.0 toman')->value());
        $this->assertSame('0', Persian::money('۰٫۰۰')->clean());
        $this->assertSame(0, Persian::money('-0.00')->value());
    }

    public function test_sign_only_money_values_are_rejected(): void
    {
        foreach (['-', '−', '- تومان', '− ریال'] as $value) {
            $money = Persian::money($value);

            $this->assertSame('', $money->clean(), $value);
            $this->assertNull($money->value(), $value);
            $this->assertSame('', $money->format(), $value);
        }
    }

    public function test_whitespace_thousands_groups_are_preserved_and_validated(): void
    {
        $this->assertSame('1000', Persian::money('1 000')->clean());
        $this->assertSame(12500, Persian::money('۱۲ ۵۰۰ تومان')->value());
        $this->assertSame(1200000, Persian::money('۱ ۲۰۰ ۰۰۰ ریال')->value());
        $this->assertSame(12000, Persian::money('12 000')->value());
        $this->assertSame(12000, Persian::money('١٢ ٠٠٠')->value());
        $this->assertSame('', Persian::money('12 34')->clean());
        $this->assertNull(Persian::money('12 34')->value());
        $this->assertSame('', Persian::money('1,000 000')->clean());
        $this->assertSame('', Persian::money('1 000,000')->clean());
    }

    public function test_natural_text_extracts_one_amount_without_merging_amounts(): void
    {
        $validAmounts = [
            'قیمت ۱۰۰ تومان' => [100, 'toman'],
            'قیمت: ۱۰۰ تومان' => [100, 'toman'],
            'مبلغ ۱,۲۵۰,۰۰۰ تومان' => [1250000, 'toman'],
            'Price: 1500 toman' => [1500, 'toman'],
            'السعر ١٢٥ ريال' => [125, 'rial'],
            'toman Price: 1500' => [1500, 'toman'],
            'تومان قیمت: ۱۰۰' => [100, 'toman'],
        ];

        foreach ($validAmounts as $input => [$value, $currency]) {
            $money = Persian::money($input);

            $this->assertSame((string) $value, $money->clean(), $input);
            $this->assertSame($value, $money->value(), $input);
            $this->assertNotSame('', $money->format(), $input);
            $this->assertSame($currency, $money->detectedCurrency(), $input);
        }

        $multipleAmounts = [
            '۱۰۰ تومان ۲۰۰ ریال',
            '۱۲ تومان ۵۰۰ ریال',
            '100 toman 200 rial',
            '۱۰۰تومان۲۰۰ریال',
            '۱۰۰ تومان ۲۰۰ تومان',
            '۱۰۰ تومان ۲۰۰',
        ];

        foreach ($multipleAmounts as $input) {
            $money = Persian::money($input);

            $this->assertSame('', $money->clean(), $input);
            $this->assertNull($money->value(), $input);
            $this->assertSame('', $money->format(), $input);
            $this->assertNull($money->detectedCurrency(), $input);
        }
    }

    public function test_data_driven_candidate_detection_consumes_all_numeric_material(): void
    {
        $validAmounts = [
            ['100', '100', 100, null],
            ['+100', '100', 100, null],
            ['-100 تومان', '-100', -100, 'toman'],
            ['12.00', '12', 12, null],
            ['-12.00', '-12', -12, null],
            ['1,000.00', '1000', 1000, null],
            ['۱۰۰ تومان', '100', 100, 'toman'],
            ['١٢٥ ريال', '125', 125, 'rial'],
            ['1 000', '1000', 1000, null],
            ["Price:\n1500 toman", '1500', 1500, 'toman'],
            ['Price, 100', '100', 100, null],
            ['Price rialish 100', '100', 100, null],
            [(string) PHP_INT_MAX, (string) PHP_INT_MAX, PHP_INT_MAX, null],
            [(string) PHP_INT_MIN, (string) PHP_INT_MIN, PHP_INT_MIN, null],
        ];

        foreach ($validAmounts as [$input, $clean, $value, $currency]) {
            $money = Persian::money($input);

            $this->assertSame($clean, $money->clean(), $input);
            $this->assertSame($value, $money->value(), $input);
            $this->assertNotSame('', $money->format(), $input);
            $this->assertSame($currency, $money->detectedCurrency(), $input);
        }

        foreach ([
            $this->incrementUnsignedIntegerString((string) PHP_INT_MAX),
            '-'.$this->incrementUnsignedIntegerString(substr((string) PHP_INT_MIN, 1)),
        ] as $overflow) {
            $money = Persian::money($overflow);

            $this->assertSame($overflow, $money->clean(), $overflow);
            $this->assertNull($money->value(), $overflow);
            $this->assertNotSame('', $money->format(), $overflow);
            $this->assertNull($money->detectedCurrency(), $overflow);
        }

        $invalidAmounts = [
            '1.0 200',
            '12.00 34',
            '1,000.00 200',
            '12 -34',
            '12 +34',
            '100 تومان 200+',
            '100 تومان 20.0.5',
            '100 تومان 12.0.abc',
            '100 toman 200',
            "100\n200",
            '12,34',
            '12 34',
            '100 تومان 200',
        ];

        foreach ($invalidAmounts as $input) {
            $money = Persian::money($input);

            $this->assertSame('', $money->clean(), $input);
            $this->assertNull($money->value(), $input);
            $this->assertSame('', $money->format(), $input);
            $this->assertNull($money->detectedCurrency(), $input);
        }
    }

    public function test_supported_digit_scripts_and_amount_forms_share_one_canonical_result(): void
    {
        $cases = [
            ['۱۲۳', '123', 123, null, '۱۲۳ تومان'],
            ['١٢٣', '123', 123, null, '۱۲۳ تومان'],
            ['123', '123', 123, null, '۱۲۳ تومان'],
            ['۱۲۵۰۰ تومان', '12500', 12500, 'toman', '۱۲,۵۰۰ تومان'],
            ['۱,۲۵۰,۰۰۰ ریال', '1250000', 1250000, 'rial', '۱,۲۵۰,۰۰۰ ریال'],
            ['12.00', '12', 12, null, '۱۲ تومان'],
            ['-۱۲۵ تومان', '-125', -125, 'toman', '-۱۲۵ تومان'],
            ['0', '0', 0, null, '۰ تومان'],
            ['-0.00', '0', 0, null, '۰ تومان'],
            ['۱۲ ۵۰۰ تومان', '12500', 12500, 'toman', '۱۲,۵۰۰ تومان'],
            ["۱۲\t۵۰۰ تومان", '12500', 12500, 'toman', '۱۲,۵۰۰ تومان'],
            ["۱۲\u{00A0}۵۰۰ تومان", '12500', 12500, 'toman', '۱۲,۵۰۰ تومان'],
        ];

        foreach ($cases as [$input, $clean, $value, $currency, $formatted]) {
            $money = Persian::money($input);

            $this->assertSame($clean, $money->clean(), $input);
            $this->assertSame($value, $money->value(), $input);
            $this->assertSame($currency, $money->detectedCurrency(), $input);
            $this->assertSame($formatted, $money->format(), $input);
        }
    }

    public function test_supported_digit_scripts_produce_identical_idempotent_canonical_values(): void
    {
        $digits = '1234567890';
        $persianDigits = '۱۲۳۴۵۶۷۸۹۰';
        $arabicDigits = '١٢٣٤٥٦٧٨٩٠';
        $representations = [
            strtr('1,234,500', array_combine(str_split($digits), preg_split('//u', $persianDigits, -1, PREG_SPLIT_NO_EMPTY))),
            strtr('1,234,500', array_combine(str_split($digits), preg_split('//u', $arabicDigits, -1, PREG_SPLIT_NO_EMPTY))),
            '1,234,500',
        ];

        foreach ($representations as $input) {
            $money = Persian::money($input);
            $canonical = $money->clean();

            $this->assertSame('1234500', $canonical, $input);
            $this->assertSame(1234500, $money->value(), $input);
            $this->assertSame($canonical, Persian::money($canonical)->clean(), $input);
        }
    }

    public function test_unsupported_unicode_numeric_scripts_are_rejected_without_partial_extraction(): void
    {
        $inputs = [
            '१२३',
            '１２３',
            '𝟙𝟚𝟛',
            '200१२३',
            '۲۰۰１２۳',
            '१२३ تومان',
            '１２３ ریال',
            'قیمت १२۳ تومان',
            'Price: 200१२۳ toman',
            '12abc१२',
        ];

        foreach ($inputs as $input) {
            $money = Persian::money($input);

            $this->assertSame('', $money->clean(), $input);
            $this->assertNull($money->value(), $input);
            $this->assertSame('', $money->format(), $input);
            $this->assertNull($money->detectedCurrency(), $input);
        }
    }

    public function test_adding_numeric_material_never_preserves_a_single_candidate(): void
    {
        foreach (['100', '۱۲۵۰۰ تومان', '-12.00'] as $amount) {
            foreach ([' + 7', ' - 3', ' 7.0', ' تومان 7 ریال', "\n8"] as $suffix) {
                $input = $amount.$suffix;
                $money = Persian::money($input);

                $this->assertSame('', $money->clean(), $input);
                $this->assertNull($money->value(), $input);
                $this->assertSame('', $money->format(), $input);
                $this->assertNull($money->detectedCurrency(), $input);
            }
        }
    }

    public function test_long_malformed_numeric_input_is_rejected(): void
    {
        $input = str_repeat('123.00.45 ', 1000);
        $money = Persian::money($input);

        $this->assertSame('', $money->clean());
        $this->assertNull($money->value());
        $this->assertSame('', $money->format());
        $this->assertNull($money->detectedCurrency());
    }

    public function test_malformed_sign_sequences_are_not_reinterpreted(): void
    {
        foreach (['--5', '-+5', '++5', '---5'] as $input) {
            $money = Persian::money($input);

            $this->assertSame('', $money->clean(), $input);
            $this->assertNull($money->value(), $input);
            $this->assertSame('', $money->format(), $input);
        }

        $this->assertSame('-5', Persian::money('-5')->clean());
        $this->assertSame(-5, Persian::money('−۵ تومان')->value());
    }

    public function test_scientific_notation_and_malformed_decimals_are_rejected(): void
    {
        $this->assertSame('', Persian::money('1e20')->clean());
        $this->assertNull(Persian::money('1e20')->value());
        $this->assertSame('', Persian::money('1e')->clean());
        $this->assertSame('', Persian::money('1e+')->clean());
        $this->assertSame('', Persian::money('12.0.5')->clean());
        $this->assertNull(Persian::money('12.0.5')->value());
        $this->assertSame('', Persian::money('1.2e3')->clean());
        $this->assertSame('', Persian::money('12.0.')->clean());
        $this->assertSame('', Persian::money('12.0.abc')->clean());
        $this->assertSame('', Persian::money('12,34')->clean());
        $this->assertSame('', Persian::money('1,00,0')->clean());
    }

    public function test_integer_overflow_is_rejected_safely(): void
    {
        $max = (string) PHP_INT_MAX;
        $minMagnitude = substr((string) PHP_INT_MIN, 1);

        $this->assertNull(Persian::money($this->incrementUnsignedIntegerString($max))->value());
        $this->assertSame(PHP_INT_MAX, Persian::money($max)->value());
        $this->assertSame(PHP_INT_MIN, Persian::money((string) PHP_INT_MIN)->value());
        $this->assertNull(Persian::money('-'.$this->incrementUnsignedIntegerString($minMagnitude))->value());
    }

    public function test_null_value(): void
    {
        $this->assertNull(Persian::money(null)->value());
    }

    private function incrementUnsignedIntegerString(string $digits): string
    {
        for ($index = strlen($digits) - 1; $index >= 0; $index--) {
            if ($digits[$index] !== '9') {
                $digits[$index] = (string) ((int) $digits[$index] + 1);

                return $digits;
            }

            $digits[$index] = '0';
        }

        return '1'.$digits;
    }
}
