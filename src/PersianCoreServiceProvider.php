<?php

namespace Zarbinco\PersianCore;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Zarbinco\PersianCore\Commands\AboutCommand;
use Zarbinco\PersianCore\Commands\DoctorCommand;
use Zarbinco\PersianCore\Commands\InstallCommand;
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
use Zarbinco\PersianCore\Formatters\MobileFormatter;
use Zarbinco\PersianCore\Formatters\MoneyFormatter;
use Zarbinco\PersianCore\Formatters\NumberFormatter;
use Zarbinco\PersianCore\Normalizers\MobileNormalizer;
use Zarbinco\PersianCore\Normalizers\MoneyNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianNormalizerPipeline;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianSearchNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;
use Zarbinco\PersianCore\Services\IranianBankDetector;

class PersianCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFromRecursive(__DIR__.'/../config/persian-core.php', 'persian-core');

        $this->app->singleton(PersianNumberNormalizer::class, function (): PersianNumberNormalizer {
            return new PersianNumberNormalizer;
        });

        $this->app->bind(PersianNumberNormalizerContract::class, PersianNumberNormalizer::class);

        $this->app->singleton(PersianSearchNormalizer::class, function ($app): PersianSearchNormalizer {
            return new PersianSearchNormalizer(
                $app->make(PersianNumberNormalizerContract::class),
                (array) config('persian-core.text', []),
            );
        });

        $this->app->bind(PersianSearchNormalizerContract::class, PersianSearchNormalizer::class);

        $this->app->singleton(PersianTextNormalizer::class, function ($app): PersianTextNormalizer {
            return new PersianTextNormalizer(
                (array) config('persian-core.text', []),
                $app->make(PersianSearchNormalizerContract::class),
            );
        });

        $this->app->bind(PersianTextNormalizerContract::class, PersianTextNormalizer::class);

        $this->app->singleton(IranianBankDetector::class, function ($app): IranianBankDetector {
            return new IranianBankDetector(
                $app->make(PersianNumberNormalizerContract::class),
            );
        });

        $this->app->bind(IranianBankDetectorContract::class, IranianBankDetector::class);

        $this->app->singleton(NumberFormatter::class, function ($app): NumberFormatter {
            return new NumberFormatter(
                $app->make(PersianNumberNormalizerContract::class),
                (array) config('persian-core.numbers', []),
            );
        });

        $this->app->bind(NumberFormatterContract::class, NumberFormatter::class);

        $this->app->singleton(MoneyNormalizer::class, function ($app): MoneyNormalizer {
            return new MoneyNormalizer(
                $app->make(PersianNumberNormalizerContract::class),
            );
        });

        $this->app->bind(MoneyNormalizerContract::class, MoneyNormalizer::class);

        $this->app->singleton(MoneyFormatter::class, function ($app): MoneyFormatter {
            return new MoneyFormatter(
                $app->make(MoneyNormalizerContract::class),
                $app->make(PersianNumberNormalizerContract::class),
                (array) config('persian-core.money', []),
                (array) config('persian-core.numbers', []),
            );
        });

        $this->app->bind(MoneyFormatterContract::class, MoneyFormatter::class);

        $this->app->singleton(MobileNormalizer::class, function ($app): MobileNormalizer {
            return new MobileNormalizer(
                $app->make(PersianNumberNormalizerContract::class),
                (array) config('persian-core.mobile', []),
            );
        });

        $this->app->bind(MobileNormalizerContract::class, MobileNormalizer::class);

        $this->app->singleton(MobileFormatter::class, function ($app): MobileFormatter {
            return new MobileFormatter(
                $app->make(MobileNormalizerContract::class),
                (array) config('persian-core.mobile', []),
            );
        });

        $this->app->bind(MobileFormatterContract::class, MobileFormatter::class);

        $this->app->singleton(PersianNormalizerPipeline::class, function ($app): PersianNormalizerPipeline {
            return new PersianNormalizerPipeline(
                $app->make(PersianTextNormalizerContract::class),
                $app->make(PersianNumberNormalizerContract::class),
                (array) config('persian-core.numbers', []),
                $app->make(PersianSearchNormalizerContract::class),
            );
        });

        $this->app->bind(PersianNormalizerPipelineContract::class, PersianNormalizerPipeline::class);

        $this->app->singleton(PersianManager::class, function ($app): PersianManager {
            return new PersianManager(
                $app->make(PersianTextNormalizerContract::class),
                $app->make(PersianNumberNormalizerContract::class),
                $app->make(NumberFormatterContract::class),
                $app->make(MoneyNormalizerContract::class),
                $app->make(MoneyFormatterContract::class),
                $app->make(MobileNormalizerContract::class),
                $app->make(MobileFormatterContract::class),
                $app->make(PersianNormalizerPipelineContract::class),
                $app->make(PersianSearchNormalizerContract::class),
                $app->make(IranianBankDetectorContract::class),
            );
        });

        $this->app->alias(PersianManager::class, 'persian-core');
    }

    private function mergeConfigFromRecursive(string $path, string $key): void
    {
        /** @var Repository $config */
        $config = $this->app['config'];
        $existing = $config->get($key, []);

        /** @var array<string, mixed> $defaults */
        $defaults = require $path;

        $config->set($key, array_replace_recursive(
            $defaults,
            is_array($existing) ? $existing : [],
        ));
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'persian-core');

        $this->publishes([
            __DIR__.'/../config/persian-core.php' => config_path('persian-core.php'),
        ], 'persian-core-config');

        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path('vendor/persian-core'),
        ], 'persian-core-lang');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                DoctorCommand::class,
                AboutCommand::class,
            ]);
        }
    }
}
