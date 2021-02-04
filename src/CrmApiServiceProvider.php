<?php

namespace NitroLab\ShellsmartAPI;

use NitroLab\ShellsmartAPI\Connector\Connector;
use NitroLab\ShellsmartAPI\Connector\Token;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class CrmApiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Token::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('crm_api.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/config.php', 'сrm_api'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Api::class];
    }

}
