<?php

namespace Zarbinco\PersianCore;

use Illuminate\Support\ServiceProvider;
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
