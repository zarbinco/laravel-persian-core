<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Data\IranianBanks;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Services\IranianBankDetector;
use Zarbinco\PersianCore\Support\IranianBank;
use Zarbinco\PersianCore\Tests\TestCase;

class IranianBankDetectorTest extends TestCase
{
    public function test_detects_bank_from_english_card_bin(): void
    {
        $this->assertSame('melli', $this->detector()->fromCard('6037991234567890')?->slug());
    }

    public function test_detects_bank_from_persian_digit_card_bin(): void
    {
        $this->assertSame('melli', $this->detector()->fromCard('۶۰۳۷۹۹۱۲۳۴۵۶۷۸۹۰')?->slug());
    }

    public function test_detects_bank_from_grouped_card_number_with_spaces_and_dashes(): void
    {
        $this->assertSame('melli', $this->detector()->fromCard('۶۰۳۷۹۹ ۱۲۳۴-۵۶۷۸ ۹۰۱۲')?->slug());
    }

    public function test_longest_bin_match_wins(): void
    {
        $detector = new IranianBankDetector(new PersianNumberNormalizer, [
            [
                'slug' => 'short',
                'name' => 'Short Bank',
                'name_fa' => 'بانک کوتاه',
                'card_bins' => ['603799'],
                'sheba_codes' => [],
            ],
            [
                'slug' => 'long',
                'name' => 'Long Bank',
                'name_fa' => 'بانک بلند',
                'card_bins' => ['60379912'],
                'sheba_codes' => [],
            ],
        ]);

        $this->assertSame('long', $detector->fromCard('6037991234567890')?->slug());
    }

    public function test_unknown_bin_returns_null(): void
    {
        $this->assertNull($this->detector()->fromCard('1111111234567890'));
    }

    public function test_fewer_than_six_card_digits_returns_null(): void
    {
        $this->assertNull($this->detector()->fromCard('60379'));
    }

    public function test_detects_bank_from_sheba_code(): void
    {
        $this->assertSame('melli', $this->detector()->fromSheba('IR170170000000000000000000')?->slug());
    }

    public function test_detects_bank_from_lowercase_ir(): void
    {
        $this->assertSame('melli', $this->detector()->fromSheba('ir170170000000000000000000')?->slug());
    }

    public function test_detects_bank_from_persian_digit_sheba(): void
    {
        $this->assertSame('melli', $this->detector()->fromSheba('IR۱۷۰۱۷۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰')?->slug());
    }

    public function test_detects_bank_from_unicode_dash_sheba(): void
    {
        $this->assertSame('melli', $this->detector()->fromSheba('IR17–0170–0000–0000–0000–0000–00')?->slug());
    }

    public function test_unknown_sheba_bank_code_returns_null(): void
    {
        $this->assertNull($this->detector()->fromSheba('IR179990000000000000000000'));
    }

    public function test_invalid_sheba_shape_returns_null(): void
    {
        $this->assertNull($this->detector()->fromSheba('IR1701700000000000000000'));
    }

    public function test_iranian_bank_to_array_returns_expected_structure(): void
    {
        $bankData = IranianBanks::bySlug('melli');

        $this->assertIsArray($bankData);

        $bank = IranianBank::fromArray($bankData);

        $this->assertSame([
            'slug' => 'melli',
            'name' => 'Bank Melli Iran',
            'name_fa' => 'بانک ملی ایران',
            'card_bins' => ['603799'],
            'sheba_codes' => ['017'],
        ], $bank->toArray());
    }

    private function detector(): IranianBankDetector
    {
        return new IranianBankDetector(new PersianNumberNormalizer);
    }
}
