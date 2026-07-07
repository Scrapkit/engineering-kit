<?php

namespace Scrapkit\EngineeringKit\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Scrapkit\EngineeringKit\EngineeringKitServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            EngineeringKitServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        config()->set('session.driver', 'array');
    }
}
