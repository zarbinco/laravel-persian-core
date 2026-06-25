<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Normalizers\PersianNormalizerPipeline;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianNormalizerPipelineTest extends TestCase
{
    public function test_default_for_storage_converts_persian_digits_to_english(): void
    {
        $pipeline = $this->pipeline();

        $this->assertSame(
            'علی کاظمی شماره 09121234567',
            $pipeline->forStorage('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷'),
        );
    }

    public function test_default_for_display_converts_english_digits_to_persian(): void
    {
        $pipeline = $this->pipeline();

        $this->assertSame(
            'علی کاظمی شماره ۰۹۱۲۱۲۳۴۵۶۷',
            $pipeline->forDisplay('علي كاظمي شماره 09121234567'),
        );
    }

    public function test_storage_digits_can_be_configured_to_persian(): void
    {
        $pipeline = $this->pipeline([
            'storage_digits' => 'fa',
        ]);

        $this->assertSame(
            'علی کاظمی شماره ۰۹۱۲۱۲۳۴۵۶۷',
            $pipeline->forStorage('علي كاظمي شماره 09121234567'),
        );
    }

    public function test_display_digits_can_be_configured_to_english(): void
    {
        $pipeline = $this->pipeline([
            'display_digits' => 'en',
        ]);

        $this->assertSame(
            'علی کاظمی شماره 09121234567',
            $pipeline->forDisplay('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷'),
        );
    }

    public function test_invalid_storage_digits_falls_back_to_english(): void
    {
        $pipeline = $this->pipeline([
            'storage_digits' => 'invalid',
        ]);

        $this->assertSame(
            'علی کاظمی شماره 09121234567',
            $pipeline->forStorage('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷'),
        );
    }

    public function test_invalid_display_digits_falls_back_to_persian(): void
    {
        $pipeline = $this->pipeline([
            'display_digits' => 'invalid',
        ]);

        $this->assertSame(
            'علی کاظمی شماره ۰۹۱۲۱۲۳۴۵۶۷',
            $pipeline->forDisplay('علي كاظمي شماره 09121234567'),
        );
    }

    public function test_service_provider_passes_number_config_to_pipeline(): void
    {
        config(['persian-core.numbers.storage_digits' => 'fa']);
        $this->app->forgetInstance(PersianNormalizerPipeline::class);

        $pipeline = $this->app->make(PersianNormalizerPipeline::class);

        $this->assertSame(
            'علی ۱۲۳',
            $pipeline->forStorage('علي 123'),
        );
    }

    /** @param array<string, mixed> $numberOptions */
    private function pipeline(array $numberOptions = []): PersianNormalizerPipeline
    {
        return new PersianNormalizerPipeline(
            new PersianTextNormalizer,
            new PersianNumberNormalizer,
            $numberOptions,
        );
    }
}
