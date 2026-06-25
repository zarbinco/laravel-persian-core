<?php

namespace Zarbinco\PersianCore\Support;

use Illuminate\Console\Command;

class ConsoleTable
{
    /**
     * @param  array<int, array{name: string, status: string, message: string, value: mixed}>  $checks
     */
    public function renderChecks(Command $command, array $checks): void
    {
        $command->table(
            ['Status', 'Check', 'Value', 'Message'],
            array_map(
                fn (array $check): array => [
                    strtoupper($check['status']),
                    $check['name'],
                    $this->stringValue($check['value']),
                    $check['message'],
                ],
                $checks,
            ),
        );
    }

    private function stringValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return get_debug_type($value);
    }
}
