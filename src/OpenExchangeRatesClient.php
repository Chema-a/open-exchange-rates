<?php

namespace Txema\OpenExchangeRates;

use GuzzleHttp\Client;

// Se eliminó Psr\Log\LoggerInterface porque no se usaba

class OpenExchangeRatesClient
{
    protected string $apiKey;
    protected Client $httpClient;

    /**

     * @param string $apiKey Api Key.
     * @param Client $httpClient GuzzleClient.
     */
    public function __construct(string $apiKey, Client $httpClient)
    {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('OpenExchangeRates API key cannot be empty.');
        }

        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }

    /**
     * Fetch exchange rate(s) from Open Exchange Rates API.
     *
     * @param string $currencyFrom Base currency
     * @param string|null $currencyTo Quote currency (optional)
     * @param bool $allCurrencies Return all rates if true
     * @return array|false
     */
    public function getRates(string $currencyFrom, ?string $currencyTo = null, bool $allCurrencies = false)
    {
        $currencyFrom = strtoupper($currencyFrom);
        $currencyTo = $currencyTo ? strtoupper($currencyTo) : null;

        $response = $this->httpClient->request(
            'GET',
            'https://openexchangerates.org/api/latest.json',
            [
                'query' => [
                    'app_id' => $this->apiKey,
                    'base'   => $currencyFrom,
                ],
            ]
        );

        $rates = json_decode($response->getBody()->getContents(), true);

        unset($rates['disclaimer'], $rates['license']);

        if (isset($rates['rates']) && count($rates['rates']) > 0) {
            if ($allCurrencies) {

                return [
                    'base'   => $rates['base'],
                    'rates'  => $rates['rates'],
                    'source' => 'primary',
                ];
            }

            if (!isset($rates['rates'][$currencyTo])) {
                return false;
            }

            return [
                'base'      => $rates['base'],
                'rate'      => $rates['rates'][$currencyTo],
                'timestamp' => $rates['timestamp'],
            ];
        }

        return false;
    }
}
