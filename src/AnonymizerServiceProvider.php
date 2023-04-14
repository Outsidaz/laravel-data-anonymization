<?php

namespace Outsidaz\LaravelDataAnonymization;

use Outsidaz\LaravelDataAnonymization\Commands\AnonymizerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AnonymizerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('anonymization')
            ->hasConfigFile('anonymizer')
            ->hasCommand(AnonymizerCommand::class);
    }
}