<?php

namespace Scrapkit\EngineeringKit;

use Scrapkit\EngineeringKit\Commands\InstallCommand;
use Scrapkit\EngineeringKit\Commands\UpdateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EngineeringKitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('engineering-kit')
            ->hasCommands([
                InstallCommand::class,
                UpdateCommand::class,
            ]);
    }
}
