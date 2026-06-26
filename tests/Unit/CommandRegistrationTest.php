<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Zarbinco\PersianCore\Tests\TestCase;

class CommandRegistrationTest extends TestCase
{
    public function test_package_commands_are_registered(): void
    {
        $commands = Artisan::all();

        $this->assertArrayHasKey('persian-core:install', $commands);
        $this->assertArrayHasKey('persian-core:doctor', $commands);
        $this->assertArrayHasKey('persian-core:about', $commands);
    }
}
