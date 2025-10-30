<?php

namespace Txema\OpenExchangeRates;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class OpenExchangeRatesClient
{
    protected string $apiKey;
    protected Client $httpClient;
    public function __construct(?Client $client = null)
    {
        // Get API key from environment
        $this->apiKey = $_ENV['OPEN_EXCHANGE_API_KEY'] ?? getenv('OPEN_EXCHANGE_API_KEY') ?? '';
        if (empty($this->apiKey)) {
            throw new \RuntimeException('OpenExchangeRates API key is not set in .env');
        }

        $this->httpClient = $client ?: new Client(['timeout' => 20]);
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

        // Make HTTP request
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
