<?php

namespace Txema\OpenExchangeRates\Facades;

use Illuminate\Support\Facades\Facade;
use Txema\OpenExchangeRates\OpenExchangeRatesClient;

/**
 * @method static array|false getRates(string $currencyFrom, ?string $currencyTo = null, bool $allCurrencies = false)
 *
 * @see \Txema\OpenExchangeRates\OpenExchangeRatesClient
 */
class Exchange extends Facade
{
    /**
     *
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return OpenExchangeRatesClient::class;
    }
}
