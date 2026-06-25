<?php

namespace Zarbinco\PersianCore\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Throwable;

class AboutCommand extends Command
{
    protected $signature = 'persian-core:about';

    protected $description = 'Show Laravel Persian Core package information.';

    public function handle(): int
    {
        $this->info('Laravel Persian Core');

        $this->table(['Item', 'Value'], [
            ['Package', 'zarbinco/laravel-persian-core'],
            ['Purpose', 'Persian text, number, mobile, money, and validation utilities for Laravel.'],
            ['Version', $this->packageVersion()],
            ['PHP', PHP_VERSION],
            ['Laravel', app()->version()],
            ['storage_digits', (string) config('persian-core.numbers.storage_digits', 'en')],
            ['display_digits', (string) config('persian-core.numbers.display_digits', 'fa')],
            ['search zwnj mode', (string) config('persian-core.text.search.zwnj', 'space')],
            ['default money currency', (string) config('persian-core.money.default_currency', 'toman')],
            ['money display digits', (string) config('persian-core.money.display_digits', 'fa')],
            ['mobile default country', (string) config('persian-core.mobile.default_country', 'IR')],
        ]);

        $this->line('Available modules: text, number, mobile, money, validation');
        $this->line('Available commands: persian-core:install, persian-core:doctor, persian-core:about');

        return self::SUCCESS;
    }

    private function packageVersion(): string
    {
        if (! class_exists(InstalledVersions::class)) {
            return 'unknown';
        }

        try {
            return InstalledVersions::getPrettyVersion('zarbinco/laravel-persian-core') ?? 'unknown';
        } catch (Throwable) {
            return 'unknown';
        }
    }
}
