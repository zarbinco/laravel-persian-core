<?php

namespace Zarbinco\PersianCore\Commands;

use Illuminate\Console\Command;
use Zarbinco\PersianCore\Support\ConfigValidator;
use Zarbinco\PersianCore\Support\ConsoleTable;

class DoctorCommand extends Command
{
    protected $signature = 'persian-core:doctor';

    protected $description = 'Inspect Laravel Persian Core setup and configuration.';

    public function __construct(
        private readonly ?ConfigValidator $validator = null,
        private readonly ?ConsoleTable $consoleTable = null,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $checks = $this->validator()->checks();

        $this->info('Laravel Persian Core doctor');
        $this->tableRenderer()->renderChecks($this, $checks);

        $hasErrors = collect($checks)->contains(fn (array $check): bool => $check['status'] === 'error');

        if ($hasErrors) {
            $this->error('One or more critical configuration checks failed.');

            return self::FAILURE;
        }

        $this->info('No critical configuration errors found.');

        return self::SUCCESS;
    }

    private function validator(): ConfigValidator
    {
        return $this->validator ?? new ConfigValidator;
    }

    private function tableRenderer(): ConsoleTable
    {
        return $this->consoleTable ?? new ConsoleTable;
    }
}
