<?php

namespace Alaaelsaid\LaravelWatiWhatsapp\Tests;

use Alaaelsaid\LaravelWatiWhatsapp\Providers\WatiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            WatiServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Whatsapp' => \Alaaelsaid\LaravelWatiWhatsapp\Facade\Whatsapp::class,
        ];
    }
}
