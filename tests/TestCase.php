<?php

namespace Laramate\FlexProperties\Tests;

use Laramate\FlexProperties\Providers\FlexPropertyServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [FlexPropertyServiceProvider::class];
    }
}