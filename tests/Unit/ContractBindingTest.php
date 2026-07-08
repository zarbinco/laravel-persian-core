<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Contracts\IranianBankDetectorContract;
use Zarbinco\PersianCore\Contracts\MobileFormatterContract;
use Zarbinco\PersianCore\Contracts\MobileNormalizerContract;
use Zarbinco\PersianCore\Contracts\MoneyFormatterContract;
use Zarbinco\PersianCore\Contracts\MoneyNormalizerContract;
use Zarbinco\PersianCore\Contracts\NumberFormatterContract;
use Zarbinco\PersianCore\Contracts\PersianNormalizerPipelineContract;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianSearchNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;
use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Formatters\MobileFormatter;
use Zarbinco\PersianCore\Formatters\MoneyFormatter;
use Zarbinco\PersianCore\Formatters\NumberFormatter;
use Zarbinco\PersianCore\Normalizers\MobileNormalizer;
use Zarbinco\PersianCore\Normalizers\MoneyNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianNormalizerPipeline;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianSearchNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;
use Zarbinco\PersianCore\PersianManager;
use Zarbinco\PersianCore\Services\IranianBankDetector;
use Zarbinco\PersianCore\Tests\TestCase;

class ContractBindingTest extends TestCase
{
    public function test_contracts_resolve_to_current_concrete_implementations(): void
    {
        foreach ($this->contractMap() as $contract => $concrete) {
            $this->assertInstanceOf($concrete, $this->app->make($contract));
        }
    }

    public function test_existing_concrete_classes_still_resolve_from_the_container(): void
    {
        foreach (array_values($this->contractMap()) as $concrete) {
            $this->assertInstanceOf($concrete, $this->app->make($concrete));
        }
    }

    public function test_manager_and_facade_public_api_still_use_documented_outputs(): void
    {
        $manager = $this->app->make(PersianManager::class);

        $this->assertSame('علی کاظمی', $manager->text('علي كاظمي')->normalize());
        $this->assertSame('123456', Persian::number('۱۲۳٤٥۶')->toEnglish());
        $this->assertSame('09121234567', Persian::mobile('۰۹۱۲ ۱۲۳ ۴۵۶۷')->normalize());
        $this->assertSame('۱,۲۵۰,۰۰۰ تومان', Persian::money(1250000)->format());
        $this->assertSame('اب میوه سن ایچ 12500 تومان', Persian::search("آب\u{200C}میوه سن\u{200C}ایچ ۱۲,۵۰۰ تومان!")->normalize());
        $this->assertSame('melli', Persian::bankFromCard('6037991234567890')?->slug());
        $this->assertSame('melli', Persian::bankFromSheba('IR170170000000000000000000')?->slug());
    }

    public function test_contract_binding_can_be_overridden_before_manager_resolution(): void
    {
        $this->app->bind(
            PersianTextNormalizerContract::class,
            fn (): PersianTextNormalizerContract => new class implements PersianTextNormalizerContract
            {
                public function normalize(string|int|float|null $value): string
                {
                    return 'custom:'.$this->stringValue($value);
                }

                public function forStorage(string|int|float|null $value): string
                {
                    return 'custom-storage:'.$this->stringValue($value);
                }

                public function forDisplay(string|int|float|null $value): string
                {
                    return 'custom-display:'.$this->stringValue($value);
                }

                public function forSearch(string|int|float|null $value): string
                {
                    return 'custom-search:'.$this->stringValue($value);
                }

                private function stringValue(string|int|float|null $value): string
                {
                    return $value === null ? '' : (string) $value;
                }
            },
        );

        $this->app->forgetInstance(PersianManager::class);
        $this->app->forgetInstance('persian-core');
        Persian::clearResolvedInstance('persian-core');

        $this->assertSame('custom:علي', Persian::text('علي')->normalize());
        $this->assertSame('custom:علي', Persian::normalize('علي')->forStorage());
    }

    public function test_bank_data_metadata_config_has_safe_informational_defaults(): void
    {
        $this->assertSame('2026-06-26', config('persian-core.bank_data.version'));
        $this->assertSame('manual-curated', config('persian-core.bank_data.source'));
        $this->assertFalse(config('persian-core.bank_data.strict_unknown'));
        $this->assertNull(Persian::bankFromCard('1111111234567890'));
    }

    /** @return array<class-string, class-string> */
    private function contractMap(): array
    {
        return [
            PersianTextNormalizerContract::class => PersianTextNormalizer::class,
            PersianNumberNormalizerContract::class => PersianNumberNormalizer::class,
            PersianSearchNormalizerContract::class => PersianSearchNormalizer::class,
            PersianNormalizerPipelineContract::class => PersianNormalizerPipeline::class,
            NumberFormatterContract::class => NumberFormatter::class,
            MobileNormalizerContract::class => MobileNormalizer::class,
            MobileFormatterContract::class => MobileFormatter::class,
            MoneyNormalizerContract::class => MoneyNormalizer::class,
            MoneyFormatterContract::class => MoneyFormatter::class,
            IranianBankDetectorContract::class => IranianBankDetector::class,
        ];
    }
}
