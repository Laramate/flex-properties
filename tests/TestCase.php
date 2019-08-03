<?php

namespace LaravelFlexProperties\Tests;

use LaravelFlexProperties\Providers\FlexPropertyServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [FlexPropertyServiceProvider::class];
    }
}