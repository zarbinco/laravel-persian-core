<?php

namespace Zarbinco\PersianCore\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use Zarbinco\PersianCore\PersianCoreServiceProvider;

abstract class TestCase extends Orchestra
{
    /** @param Application $app */
    protected function getPackageProviders($app): array
    {
        return [
            PersianCoreServiceProvider::class,
        ];
    }
}
