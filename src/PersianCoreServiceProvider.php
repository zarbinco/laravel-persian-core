<?php

namespace Zarbinco\PersianCore;

use Illuminate\Support\ServiceProvider;
use Zarbinco\PersianCore\Formatters\MobileFormatter;
use Zarbinco\PersianCore\Formatters\NumberFormatter;
use Zarbinco\PersianCore\Normalizers\MobileNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianNormalizerPipeline;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;

class PersianCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/persian-core.php', 'persian-core');

        $this->app->singleton(PersianTextNormalizer::class, function (): PersianTextNormalizer {
            return new PersianTextNormalizer((array) config('persian-core.text', []));
        });

        $this->app->singleton(PersianNumberNormalizer::class, function (): PersianNumberNormalizer {
            return new PersianNumberNormalizer;
        });

        $this->app->singleton(NumberFormatter::class, function ($app): NumberFormatter {
            return new NumberFormatter(
                $app->make(PersianNumberNormalizer::class),
                (array) config('persian-core.numbers', []),
            );
        });

        $this->app->singleton(MobileNormalizer::class, function ($app): MobileNormalizer {
            return new MobileNormalizer(
                $app->make(PersianNumberNormalizer::class),
                (array) config('persian-core.mobile', []),
            );
        });

        $this->app->singleton(MobileFormatter::class, function ($app): MobileFormatter {
            return new MobileFormatter(
                $app->make(MobileNormalizer::class),
                (array) config('persian-core.mobile', []),
            );
        });

        $this->app->singleton(PersianNormalizerPipeline::class, function ($app): PersianNormalizerPipeline {
            return new PersianNormalizerPipeline(
                $app->make(PersianTextNormalizer::class),
                $app->make(PersianNumberNormalizer::class),
                (array) config('persian-core.numbers', []),
            );
        });

        $this->app->singleton(PersianManager::class, function ($app): PersianManager {
            return new PersianManager(
                $app->make(PersianTextNormalizer::class),
                $app->make(PersianNumberNormalizer::class),
                $app->make(NumberFormatter::class),
                $app->make(MobileNormalizer::class),
                $app->make(MobileFormatter::class),
                $app->make(PersianNormalizerPipeline::class),
            );
        });

        $this->app->alias(PersianManager::class, 'persian-core');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/persian-core.php' => config_path('persian-core.php'),
        ], 'persian-core-config');
    }
}
