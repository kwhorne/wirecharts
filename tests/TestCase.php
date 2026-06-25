<?php

namespace WireCharts\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use WireCharts\WireChartsServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            WireChartsServiceProvider::class,
        ];
    }
}
