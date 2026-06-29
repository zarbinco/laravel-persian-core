<?php

namespace Zarbinco\PersianCore;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Zarbinco\PersianCore\Commands\AboutCommand;
use Zarbinco\PersianCore\Commands\DoctorCommand;
use Zarbinco\PersianCore\Commands\InstallCommand;
use Zarbinco\PersianCore\Formatters\MobileFormatter;
use Zarbinco\PersianCore\Formatters\MoneyFormatter;
use Zarbinco\PersianCore\Formatters\NumberFormatter;
use Zarbinco\PersianCore\Normalizers\MobileNormalizer;
use Zarbinco\PersianCore\Normalizers\MoneyNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianNormalizerPipeline;
use Zarbinco\PersianCore\Normalizers\PersianNumberNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianSearchNormalizer;
use Zarbinco\PersianCore\Normalizers\PersianTextNormalizer;

class PersianCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFromRecursive(__DIR__.'/../config/persian-core.php', 'persian-core');

        $this->app->singleton(PersianTextNormalizer::class, function (): PersianTextNormalizer {
            return new PersianTextNormalizer((array) config('persian-core.text', []));
        });

        $this->app->singleton(PersianNumberNormalizer::class, function (): PersianNumberNormalizer {
            return new PersianNumberNormalizer;
        });

        $this->app->singleton(PersianSearchNormalizer::class, function ($app): PersianSearchNormalizer {
            return new PersianSearchNormalizer(
                $app->make(PersianNumberNormalizer::class),
                (array) config('persian-core.text', []),
            );
        });

        $this->app->singleton(NumberFormatter::class, function ($app): NumberFormatter {
            return new NumberFormatter(
                $app->make(PersianNumberNormalizer::class),
                (array) config('persian-core.numbers', []),
            );
        });

        $this->app->singleton(MoneyNormalizer::class, function ($app): MoneyNormalizer {
            return new MoneyNormalizer(
                $app->make(PersianNumberNormalizer::class),
            );
        });

        $this->app->singleton(MoneyFormatter::class, function ($app): MoneyFormatter {
            return new MoneyFormatter(
                $app->make(MoneyNormalizer::class),
                $app->make(PersianNumberNormalizer::class),
                (array) config('persian-core.money', []),
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
                $app->make(PersianSearchNormalizer::class),
            );
        });

        $this->app->singleton(PersianManager::class, function ($app): PersianManager {
            return new PersianManager(
                $app->make(PersianTextNormalizer::class),
                $app->make(PersianNumberNormalizer::class),
                $app->make(NumberFormatter::class),
                $app->make(MoneyNormalizer::class),
                $app->make(MoneyFormatter::class),
                $app->make(MobileNormalizer::class),
                $app->make(MobileFormatter::class),
                $app->make(PersianNormalizerPipeline::class),
                $app->make(PersianSearchNormalizer::class),
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
