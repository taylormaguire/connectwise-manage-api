<?php

namespace taylormaguire\CWManageAPI;

use Illuminate\Support\ServiceProvider;
use taylormaguire\CWManageAPI\CWManageAPI;

class ConnectWiseManageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('CWManageAPI', function() {
            return new CWManageAPI();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'cw_manage_api');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
