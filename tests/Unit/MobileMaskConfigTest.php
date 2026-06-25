<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Formatters\MobileFormatter;
use Zarbinco\PersianCore\PersianManager;
use Zarbinco\PersianCore\Tests\TestCase;

class MobileMaskConfigTest extends TestCase
{
    public function test_default_mask_remains_unchanged(): void
    {
        $this->assertSame('0912***4567', Persian::mobile('09121234567')->mask());
    }

    public function test_custom_valid_mask_pattern(): void
    {
        config(['persian-core.mobile.iran.mask_pattern' => '09*******67']);
        $this->refreshPersianFacade();

        $this->assertSame('09*******67', Persian::mobile('09121234567')->mask());
    }

    public function test_invalid_mask_pattern_falls_back(): void
    {
        config(['persian-core.mobile.iran.mask_pattern' => 'invalid']);
        $this->refreshPersianFacade();

        $this->assertSame('0912***4567', Persian::mobile('09121234567')->mask());
    }

    public function test_explicit_mask_argument_has_priority(): void
    {
        config(['persian-core.mobile.iran.mask_pattern' => '09*******67']);
        $this->refreshPersianFacade();

        $this->assertSame('091****4567', Persian::mobile('09121234567')->mask('091****4567'));
    }

    private function refreshPersianFacade(): void
    {
        $this->app->forgetInstance(MobileFormatter::class);
        $this->app->forgetInstance(PersianManager::class);
        Persian::clearResolvedInstance('persian-core');
    }
}
