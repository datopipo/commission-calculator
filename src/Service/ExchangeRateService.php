<?php

declare(strict_types=1);

namespace CommissionCalculator\Service;

use CommissionCalculator\Interface\AppConfigInterface;
use CommissionCalculator\Interface\ExchangeRateServiceInterface;
use RuntimeException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ExchangeRateService implements ExchangeRateServiceInterface
{
    private array $rates = [];
    private const CACHE_FILE = __DIR__ . '/../../var/cache/exchange_rates.json';

    public function __construct(
        protected AppConfigInterface $config,
        private readonly ?Client $httpClient = null
    ) {
        $client = $this->httpClient ?? new Client();
        $this->loadRates($client);
    }

    private function loadRates(Client $client): void
    {
        try {
            $apiKey = $_ENV['EXCHANGE_RATE_API_KEY'] ?? null;
            $apiUrl = $_ENV['EXCHANGE_RATE_API_URL'];
            
            $currencies = implode(',', $this->config->getSupportedCurrencies());
            $response = $client->get($apiUrl, [
                'query' => [
                    'access_key' => $apiKey,
                    'currencies' => $currencies
                ]
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['success']) || !$data['success']) {
                throw new RuntimeException('API request failed');
            }

            if (!isset($data['quotes']) || !is_array($data['quotes'])) {
                throw new RuntimeException('Invalid API response format');
            }

            $quotes = $data['quotes'];
            $usdToEur = $quotes['USDEUR'] ?? null;
            
            if ($usdToEur === null) {
                throw new RuntimeException('EUR rate not found in API response');
            }

            // Convert USD-based rates to EUR-based rates
            foreach ($quotes as $pair => $rate) {
                if (str_starts_with($pair, 'USD')) {
                    $currency = substr($pair, 3);
                    $this->rates[$currency] = $rate / $usdToEur;
                }
            }
            // Add EUR as base currency
            $this->rates['EUR'] = 1.0;
            // Add USD as base currency
            $this->rates['USD'] = 1.0;

            file_put_contents(self::CACHE_FILE, json_encode(['rates' => $this->rates]));
        } catch (GuzzleException|RuntimeException) {
            if (file_exists(self::CACHE_FILE)) {
                $data = json_decode(file_get_contents(self::CACHE_FILE), true);
                $this->rates = $data['rates'] ?? [];
            }
        }
    }

    public function getRate(string $currency): float
    {
        if ($currency === 'EUR') {
            return 1.0;
        }

        if (!in_array($currency, $this->config->getSupportedCurrencies(), true)) {
            throw new RuntimeException("Currency not supported: $currency");
        }

        if (!isset($this->rates[$currency])) {
            throw new RuntimeException("Exchange rate not available for currency: $currency");
        }

        return $this->rates[$currency];
    }
}