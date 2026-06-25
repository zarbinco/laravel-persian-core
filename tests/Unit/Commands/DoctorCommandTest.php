<?php

namespace Zarbinco\PersianCore\Tests\Unit\Commands;

use Zarbinco\PersianCore\Support\ConfigValidator;
use Zarbinco\PersianCore\Tests\TestCase;

class DoctorCommandTest extends TestCase
{
    public function test_doctor_command_returns_success_with_default_config(): void
    {
        $this->artisan('persian-core:doctor')
            ->assertExitCode(0);
    }

    public function test_doctor_detects_invalid_storage_digits(): void
    {
        config(['persian-core.numbers.storage_digits' => 'invalid']);

        $this->artisan('persian-core:doctor')
            ->assertExitCode(1);
    }

    public function test_doctor_detects_invalid_search_zwnj(): void
    {
        config(['persian-core.text.search.zwnj' => 'invalid']);

        $this->artisan('persian-core:doctor')
            ->assertExitCode(1);
    }

    public function test_doctor_detects_invalid_money_default_currency(): void
    {
        config(['persian-core.money.default_currency' => 'invalid']);

        $this->artisan('persian-core:doctor')
            ->assertExitCode(1);
    }

    public function test_doctor_translation_check_passes(): void
    {
        $check = collect((new ConfigValidator)->checks())
            ->firstWhere('name', 'translations.validation');

        $this->assertSame('ok', $check['status'] ?? null);
    }
}
