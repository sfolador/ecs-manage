<?php

namespace Sfolador\EcsManage;

use Aws\Laravel\AwsServiceProvider;
use Sfolador\EcsManage\Commands\EcsManageCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EcsManageServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ecs-manage')
            ->hasConfigFile()
            ->hasCommand(EcsManageCommand::class);
    }

    public function registeringPackage(): void
    {
        $this->app->bind(EcsManageInterface::class, function () {
            return new EcsManage();
        });

        $this->app->register(AwsServiceProvider::class);
    }
}
