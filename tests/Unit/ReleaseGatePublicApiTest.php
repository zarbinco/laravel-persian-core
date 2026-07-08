<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Illuminate\Support\ServiceProvider;
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
use Zarbinco\PersianCore\PersianCoreServiceProvider;
use Zarbinco\PersianCore\PersianManager;
use Zarbinco\PersianCore\Rules\IranianCardNumber;
use Zarbinco\PersianCore\Rules\IranianMobile;
use Zarbinco\PersianCore\Rules\IranianNationalCode;
use Zarbinco\PersianCore\Rules\IranianPostalCode;
use Zarbinco\PersianCore\Rules\IranianSheba;
use Zarbinco\PersianCore\Rules\PersianAlpha;
use Zarbinco\PersianCore\Rules\PersianAlphaNum;
use Zarbinco\PersianCore\Rules\PersianMoneyAmount;
use Zarbinco\PersianCore\Rules\PersianText;
use Zarbinco\PersianCore\Tests\TestCase;

class ReleaseGatePublicApiTest extends TestCase
{
    public function test_documented_facade_and_manager_methods_remain_available(): void
    {
        foreach ($this->documentedMethods() as $method) {
            $this->assertTrue(method_exists(PersianManager::class, $method), "{$method} is missing from PersianManager.");
        }

        $this->assertSame('علی کاظمی شماره 09121234567', Persian::normalize('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷')->forStorage());
        $this->assertSame('علی کاظمی شماره 09121234567', Persian::clean('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷'));
        $this->assertSame('اب میوه 12500 تومان', Persian::searchable("آب\u{200C}میوه ۱۲,۵۰۰ تومان!"));
        $this->assertSame(['اب', 'میوه'], Persian::search("آب\u{200C}میوه")->tokens());
        $this->assertSame('علی کاظمی', Persian::text('علي كاظمي')->normalize());
        $this->assertSame('123456', Persian::number('۱۲۳٤٥۶')->toEnglish());
        $this->assertSame('+989121234567', Persian::mobile('۰۹۱۲ ۱۲۳ ۴۵۶۷')->international());
        $this->assertSame('۱,۲۵۰,۰۰۰ تومان', Persian::money(1250000)->format());
        $this->assertSame('melli', Persian::card('6037991234567890')->bankSlug());
        $this->assertSame('melli', Persian::sheba('IR170170000000000000000000')->bankSlug());
        $this->assertSame('melli', Persian::bankFromCard('6037991234567890')?->slug());
        $this->assertSame('melli', Persian::bankFromSheba('IR170170000000000000000000')?->slug());
    }

    public function test_documented_validation_rules_remain_available(): void
    {
        foreach ($this->validationRules() as $rule) {
            $this->assertTrue(class_exists($rule), "{$rule} is missing.");
        }

        $this->assertTrue(validator(['mobile' => '09121234567'], ['mobile' => [new IranianMobile]])->passes());
        $this->assertTrue(validator(['amount' => '۱,۲۵۰,۰۰۰ تومان'], ['amount' => [new PersianMoneyAmount]])->passes());
    }

    public function test_documented_extension_contracts_resolve_from_container(): void
    {
        foreach ($this->contracts() as $contract) {
            $this->assertInstanceOf($contract, $this->app->make($contract));
        }
    }

    public function test_publish_tags_are_registered_for_config_and_lang(): void
    {
        $this->assertNotEmpty(ServiceProvider::pathsToPublish(PersianCoreServiceProvider::class, 'persian-core-config'));
        $this->assertNotEmpty(ServiceProvider::pathsToPublish(PersianCoreServiceProvider::class, 'persian-core-lang'));
    }

    /** @return array<int, string> */
    private function documentedMethods(): array
    {
        return [
            'normalize',
            'clean',
            'searchable',
            'search',
            'text',
            'number',
            'mobile',
            'money',
            'card',
            'sheba',
            'bankFromCard',
            'bankFromSheba',
        ];
    }

    /** @return array<int, class-string> */
    private function validationRules(): array
    {
        return [
            PersianText::class,
            PersianAlpha::class,
            PersianAlphaNum::class,
            IranianMobile::class,
            IranianNationalCode::class,
            IranianPostalCode::class,
            IranianSheba::class,
            IranianCardNumber::class,
            PersianMoneyAmount::class,
        ];
    }

    /** @return array<int, class-string> */
    private function contracts(): array
    {
        return [
            PersianTextNormalizerContract::class,
            PersianNumberNormalizerContract::class,
            PersianSearchNormalizerContract::class,
            PersianNormalizerPipelineContract::class,
            NumberFormatterContract::class,
            MobileNormalizerContract::class,
            MobileFormatterContract::class,
            MoneyNormalizerContract::class,
            MoneyFormatterContract::class,
            IranianBankDetectorContract::class,
        ];
    }
}
