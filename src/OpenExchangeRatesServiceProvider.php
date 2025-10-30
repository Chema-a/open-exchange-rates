<?php

namespace Txema\OpenExchangeRates;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class OpenExchangeRatesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/openexchangerate.php',
            'openexchangerate'
        );

        $this->app->singleton(OpenExchangeRatesClient::class, function ($app) {

            $config = $app->make('config');
            $apiKey = $config->get('openexchangerate.api_key');
            $timeout = $config->get('openexchangerate.timeout', 20);

            if (empty($apiKey)) {
                throw new \RuntimeException('OpenExchangeRates API key no está definida en config/openexchangerate.php');
            }

            $guzzleClient = new Client(['timeout' => $timeout]);

            return new OpenExchangeRatesClient(
                $apiKey,
                $guzzleClient
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/config/openexchangerate.php' => $this->app->configPath('openexchangerate.php'),
            ], 'config');
        }
    }
}
