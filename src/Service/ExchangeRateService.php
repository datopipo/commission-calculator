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
            // Try to get rates from API
            $apiUrl = $_ENV['EXCHANGE_RATE_API_URL'] ?? 'https://api.exchangerate.host/latest';
            $response = $client->get($apiUrl, [
                'query' => [
                    'base' => 'EUR',
                    'symbols' => implode(',', $this->config->getSupportedCurrencies())
                ]
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['rates']) && is_array($data['rates'])) {
                $this->rates = $data['rates'];
                $this->rates['EUR'] = 1.0; // Ensure EUR base rate is set
                
                // Cache the rates
                if (!file_exists(dirname(self::CACHE_FILE))) {
                    mkdir(dirname(self::CACHE_FILE), 0777, true);
                }
                file_put_contents(self::CACHE_FILE, json_encode(['rates' => $this->rates]));
            } else {
                throw new RuntimeException('Invalid API response format');
            }
        } catch (GuzzleException|RuntimeException $e) {
            // API failed, try to load from cache
            if (file_exists(self::CACHE_FILE)) {
                $cached = json_decode(file_get_contents(self::CACHE_FILE), true);
                if (isset($cached['rates'])) {
                    $this->rates = $cached['rates'];
                    return;
                }
            }
            
            // If cache failed, use fixed rates from config
            $this->rates = $this->config->getFixedRates();
            if (empty($this->rates)) {
                throw new RuntimeException("No exchange rates available - API failed and no cached/fixed rates found");
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

        return (float)$this->rates[$currency];
    }
}