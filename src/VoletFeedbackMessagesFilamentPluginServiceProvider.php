<?php

namespace Mydnic\VoletFeedbackMessagesFilamentPlugin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VoletFeedbackMessagesFilamentPluginServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('volet-feedback-messages-filament-plugin');
    }
}
