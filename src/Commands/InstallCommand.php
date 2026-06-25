<?php

namespace Zarbinco\PersianCore\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'persian-core:install {--force : Overwrite existing published files}';

    protected $description = 'Publish Laravel Persian Core config and translations.';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $this->callSilent('vendor:publish', [
            '--tag' => 'persian-core-config',
            '--force' => $force,
        ]);

        $this->callSilent('vendor:publish', [
            '--tag' => 'persian-core-lang',
            '--force' => $force,
        ]);

        $this->info('Laravel Persian Core installed successfully.');
        $this->line('');
        $this->line('Next steps:');
        $this->line("Persian::text('علي')->normalize();");
        $this->line("Persian::number('۱۲۳')->toEnglish();");
        $this->line("Persian::mobile('۰۹۱۲۱۲۳۴۵۶۷')->national();");
        $this->line("Persian::money('۱,۲۵۰,۰۰۰ تومان')->value();");

        return self::SUCCESS;
    }
}
