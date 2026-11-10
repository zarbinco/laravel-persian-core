<?php

namespace Zarbinco\PersianCore\Tests\Unit\Rules;

use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Data\IranianBanks;
use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Rules\IranianCardNumber;
use Zarbinco\PersianCore\Tests\TestCase;

class IranianCardNumberRuleTest extends TestCase
{
    public function test_passes_for_valid_luhn_card(): void
    {
        $this->assertTrue($this->passes('6037990000000006'));
    }

    public function test_passes_for_supported_saderat_bin(): void
    {
        $this->assertTrue($this->passes($this->validLuhnCardForBin('603769')));
    }

    public function test_passes_for_persian_digits(): void
    {
        $this->assertTrue($this->passes('۶۰۳۷۹۹۰۰۰۰۰۰۰۰۰۶'));
    }

    public function test_accepts_every_supported_bank_bin_when_bin_required(): void
    {
        config(['persian-core.validation.iranian_card_number.require_iranian_bin' => true]);

        foreach (IranianBanks::cardBins() as $bin) {
            $card = $this->validLuhnCardForBin($bin);

            $this->assertTrue($this->passes($card), $bin);
            $this->assertNotNull(Persian::card($card)->bank(), $bin);
            $this->assertSame(IranianBanks::slugByCardBin($bin), Persian::card($card)->bankSlug(), $bin);
        }
    }

    public function test_fails_for_invalid_luhn(): void
    {
        config(['persian-core.validation.iranian_card_number.require_luhn' => true]);

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

    public function test_unknown_bin_behavior_matches_configuration_and_bank_lookup(): void
    {
        $card = '4111111111111111';

        config(['persian-core.validation.iranian_card_number.require_iranian_bin' => false]);
        $this->assertTrue($this->passes($card));
        $this->assertNull(Persian::card($card)->bank());
        $this->assertNull(Persian::card($card)->bankSlug());

        config(['persian-core.validation.iranian_card_number.require_iranian_bin' => true]);
        $this->assertFalse($this->passes($card));
    }

    private function passes(mixed $value): bool
    {
        return Validator::make(['card' => $value], ['card' => [new IranianCardNumber]])->passes();
    }

    private function validLuhnCardForBin(string $bin): string
    {
        $prefix = str_pad($bin, 6, '0', STR_PAD_RIGHT);
        $base = substr($prefix.'123456789', 0, 15);
        $sum = 0;

        foreach (str_split($base) as $index => $digit) {
            $number = (int) $digit;

            if ($index % 2 === 0) {
                $number *= 2;

                if ($number > 9) {
                    $number -= 9;
                }
            }

            $sum += $number;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $base.(string) $checkDigit;
    }
}
