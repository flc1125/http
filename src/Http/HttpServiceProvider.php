<?php

namespace Flc\Http;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * CLIENT 服务者
 *
 * @author Flc <i@flc.io>
 */
class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $source = realpath($raw = __DIR__.'/../config/http.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('http.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('http');
        }

        $this->mergeConfigFrom($source, 'http');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('http', function ($app) {
            return new Client($app);
        });
    }
}
