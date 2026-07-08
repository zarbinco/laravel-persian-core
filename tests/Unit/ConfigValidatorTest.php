<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Support\ConfigValidator;
use Zarbinco\PersianCore\Tests\TestCase;

class ConfigValidatorTest extends TestCase
{
    public function test_default_config_has_no_errors(): void
    {
        $this->assertFalse($this->hasErrors());
    }

    public function test_invalid_storage_digits_produces_error(): void
    {
        config(['persian-core.numbers.storage_digits' => 'invalid']);

        $this->assertCheckStatus('numbers.storage_digits', 'error');
    }

    public function test_invalid_display_digits_produces_error(): void
    {
        config(['persian-core.numbers.display_digits' => 'invalid']);

        $this->assertCheckStatus('numbers.display_digits', 'error');
    }

    public function test_invalid_search_zwnj_produces_error(): void
    {
        config(['persian-core.text.search.zwnj' => 'invalid']);

        $this->assertCheckStatus('text.search.zwnj', 'error');
    }

    public function test_invalid_search_madda_alef_config_produces_error(): void
    {
        config(['persian-core.text.search.normalize_madda_alef' => 'yes']);

        $this->assertCheckStatus('text.search.normalize_madda_alef', 'error');
    }

    public function test_invalid_money_currency_produces_error(): void
    {
        config(['persian-core.money.default_currency' => 'invalid']);

        $this->assertCheckStatus('money.default_currency', 'error');
    }

    public function test_invalid_rial_to_toman_rate_produces_error(): void
    {
        config(['persian-core.money.rial_to_toman_rate' => 0]);

        $this->assertCheckStatus('money.rial_to_toman_rate', 'error');
    }

    public function test_invalid_banks_unknown_returns_null_config_produces_error(): void
    {
        config(['persian-core.banks.unknown_returns_null' => 'yes']);

        $this->assertCheckStatus('banks.unknown_returns_null', 'error');
    }

    public function test_invalid_bank_data_metadata_produces_errors(): void
    {
        config([
            'persian-core.bank_data.version' => null,
            'persian-core.bank_data.source' => null,
            'persian-core.bank_data.strict_unknown' => 'yes',
        ]);

        $this->assertCheckStatus('bank_data.version', 'error');
        $this->assertCheckStatus('bank_data.source', 'error');
        $this->assertCheckStatus('bank_data.strict_unknown', 'error');
    }

    public function test_invalid_validation_strict_produces_error(): void
    {
        config(['persian-core.validation.strict' => 'yes']);

        $this->assertCheckStatus('validation.strict', 'error');
    }

    public function test_ext_intl_check_is_not_error(): void
    {
        $this->assertContains($this->check('extension.intl')['status'], ['ok', 'info']);
    }

    private function hasErrors(): bool
    {
        return collect((new ConfigValidator)->checks())
            ->contains(fn (array $check): bool => $check['status'] === 'error');
    }

    private function assertCheckStatus(string $name, string $status): void
    {
        $this->assertSame($status, $this->check($name)['status']);
    }

    /** @return array{name: string, status: string, message: string, value: mixed} */
    private function check(string $name): array
    {
        $check = collect((new ConfigValidator)->checks())->firstWhere('name', $name);

        $this->assertIsArray($check);

        return $check;
    }
}
