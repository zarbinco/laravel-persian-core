<?php

namespace Zarbinco\PersianCore\Tests\Unit\Commands;

use Zarbinco\PersianCore\Tests\TestCase;

class InstallCommandTest extends TestCase
{
    public function test_install_command_exists_and_returns_success(): void
    {
        $this->artisan('persian-core:install')
            ->assertExitCode(0);
    }

    public function test_install_command_supports_force(): void
    {
        $this->artisan('persian-core:install', ['--force' => true])
            ->assertExitCode(0);
    }
}
