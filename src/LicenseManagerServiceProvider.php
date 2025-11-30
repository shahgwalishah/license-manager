<?php

namespace Wijhat\LicenseManager;

use Illuminate\Support\ServiceProvider;
use Wijhat\LicenseManager\Bootstrap\SystemGuard;

class LicenseManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/license-manager.php',
            'license-manager'
        );
    }

    public function boot()
    {
        SystemGuard::check();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'license-manager');
    }
}
