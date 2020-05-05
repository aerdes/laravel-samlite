<?php

namespace Aerdes\LaravelSamlite;

use Aerdes\LaravelSamlite\Console\Commands\SamlSetupCommand;
use Illuminate\Support\ServiceProvider;
use OneLogin\Saml2\Utils as OneLogin_Saml2_Utils;

class SamlServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services
     */
    public function boot()
    {
        // Loading the routes
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Setting proxy vars
        if (config('saml.proxy_vars')) {
            OneLogin_Saml2_Utils::setProxyVars(true);
        }

        if ($this->app->runningInConsole()) {
            // Publishing the config
            $this->publishes([
                __DIR__.'/../config/saml.php' => config_path('saml.php'),
            ], 'config');
            // Registering the package commands
            $this->commands([
                SamlSetupCommand::class
            ]);
        }
    }

    /**
     * Register the application services
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/saml.php', 'saml');

        // Register the main class to use with the facade
        $this->app->singleton(SamlAuth::class, function ($app) {
            // Retrieve IDP
            $idp = $app->request->route('idp');
            // Check if IDP is setup in the config
            if(!array_key_exists($idp, $app->config['saml.idps'])) {
                abort(404);
            }
            // Create SamlAuth instance
            return new SamlAuth($idp);
        });
    }

}
