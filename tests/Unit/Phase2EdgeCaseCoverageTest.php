<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Rules\IranianCardNumber;
use Zarbinco\PersianCore\Rules\IranianMobile;
use Zarbinco\PersianCore\Rules\IranianNationalCode;
use Zarbinco\PersianCore\Rules\IranianPostalCode;
use Zarbinco\PersianCore\Rules\IranianSheba;
use Zarbinco\PersianCore\Rules\PersianMoneyAmount;
use Zarbinco\PersianCore\Tests\TestCase;

class Phase2EdgeCaseCoverageTest extends TestCase
{
    public function test_text_normalization_handles_mixed_scripts_spacing_diacritics_and_zwnj(): void
    {
        $this->assertSame(
            "کیک شکلاتی Laravel\u{200C}Core",
            Persian::text("  كیك\u{0650}   شکلاتي  Laravel\u{200C}Core  ")->normalize(),
        );
    }

    public function test_display_normalization_cleans_common_punctuation_spacing(): void
    {
        $this->assertSame(
            'سلام، علی! خوبی؟',
            Persian::text('سلام ،  علي  ! خوبی ؟')->forDisplay(),
        );
    }

    public function test_digit_normalization_handles_persian_arabic_english_and_text_context(): void
    {
        $this->assertSame('کد 12345 ABC 678', Persian::number('کد ۱۲۳٤٥ ABC 678')->toEnglish());
        $this->assertSame('Code ۱۲۳۴۵', Persian::number('Code 123٤٥')->toPersian());
        $this->assertSame('123456', Persian::number('شماره ۱۲ ٣۴-AB56')->digitsOnly());
    }

    public function test_search_normalization_is_deterministic_for_mixed_text_digits_and_punctuation(): void
    {
        $search = "  كیك\u{0650}،  سن\u{200C}ایچ ۱۲,۵۰۰!!! PHP  ";
        $normalized = Persian::search($search)->normalize();

        $this->assertSame('کیک سن ایچ 12500 PHP', $normalized);
        $this->assertSame($normalized, Persian::search($normalized)->normalize());
        $this->assertSame(['کیک', 'سن', 'ایچ', '12500', 'PHP'], Persian::search($search)->tokens());
    }

    public function test_mobile_normalization_accepts_documented_forms_and_keeps_invalid_values_distinct_from_validation(): void
    {
        $this->assertSame('09121234567', Persian::mobile('09121234567')->normalize());
        $this->assertSame('09121234567', Persian::mobile('989121234567')->national());
        $this->assertSame('09121234567', Persian::mobile('+989121234567')->national());
        $this->assertSame('09121234567', Persian::mobile('(۰۹۱۲) ۱۲۳-۴۵۶۷')->normalize());

        $this->assertSame('0912', Persian::mobile('0912')->normalize());
        $this->assertFalse($this->passes('mobile', '0912', new IranianMobile));
    }

    public function test_money_formatting_handles_zero_negative_amounts_and_digit_modes(): void
    {
        $this->assertSame('۰ تومان', Persian::money(0)->format());
        $this->assertSame('-۱,۲۵۰ تومان', Persian::money(-1250)->format());
        $this->assertSame('1,250 rial', Persian::money(1250)->format('rial', 'en'));
        $this->assertSame(1250000, Persian::money('  ۱,۲۵۰,۰۰۰   تومان ')->value());
    }

    public function test_validation_rules_cover_documented_shapes_without_live_ownership_checks(): void
    {
        $this->assertTrue($this->passes('mobile', '(0912) 123 4567', new IranianMobile));
        $this->assertTrue($this->passes('national_code', '۰۱۲-۳۴۵-۶۷۸۹', new IranianNationalCode));
        $this->assertTrue($this->passes('postal_code', '۱۲۳۴۵-۶۷۸۹۰', new IranianPostalCode));
        $this->assertTrue($this->passes('card', '۶۰۳۷-۹۹۰۰-۰۰۰۰-۰۰۰۶', new IranianCardNumber));
        $this->assertTrue($this->passes('sheba', 'ir۱۸-۰۱۰۰-۰۰۰۰-۰۰۰۰-۰۰۰۰-۰۰۰۰-۰۰', new IranianSheba));
        $this->assertTrue($this->passes('amount', '۱,۲۵۰,۰۰۰ تومان', new PersianMoneyAmount));

        $this->assertFalse($this->passes('mobile', 'abc09121234567def', new IranianMobile));
        $this->assertTrue($this->passes('mobile', 'abc09121234567def', new IranianMobile(strict: false)));
    }

    public function test_bank_detection_is_offline_metadata_and_returns_null_for_unknown_or_invalid_shapes(): void
    {
        $this->assertSame('melli', Persian::card('۶۰۳۷۹۹ ۰۰۰۰ ۰۰۰۰ ۰۰۰۶')->bankSlug());
        $this->assertSame('melli', Persian::sheba('IR17 0170 0000 0000 0000 0000 00')->bankSlug());

        $this->assertNull(Persian::card('1111110000000000')->bank());
        $this->assertNull(Persian::card('60379')->bank());
        $this->assertNull(Persian::sheba('IR1701700000000000000000')->bank());
    }

    private function passes(string $attribute, mixed $value, ValidationRule $rule): bool
    {
        return Validator::make([$attribute => $value], [$attribute => [$rule]])->passes();
    }
}
