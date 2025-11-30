<?php

namespace Illuminates\Framework;

use Illuminate\Support\ServiceProvider;
use Illuminates\Framework\Bootstrap\SystemGuard;

class IlluminatesManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/manager.php',
            'manager'
        );
    }

    public function boot()
    {
        SystemGuard::check();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'manager');
    }
}
