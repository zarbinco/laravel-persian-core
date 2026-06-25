<?php

namespace Zarbinco\PersianCore\Tests\Unit\Commands;

use Zarbinco\PersianCore\Tests\TestCase;

class AboutCommandTest extends TestCase
{
    public function test_about_command_returns_success(): void
    {
        $this->artisan('persian-core:about')
            ->assertExitCode(0);
    }
}
