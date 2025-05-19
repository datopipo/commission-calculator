<?php

declare(strict_types=1);

namespace CommissionCalculator\Service;

use CommissionCalculator\Interface\AppConfigInterface;
use CommissionCalculator\Interface\CurrencyConverterInterface;
use CommissionCalculator\Exception\InvalidInputException;
use CommissionCalculator\Interface\ExchangeRateServiceInterface;

class CurrencyConverter implements CurrencyConverterInterface
{
    public function __construct(
        private readonly ExchangeRateServiceInterface $exchangeRateService,
        private readonly AppConfigInterface $config
    ) {}

    /**
     * Convert amount to EUR
     * @throws InvalidInputException
     */
    public function convertToEur(float $amount, string $fromCurrency): float
    {
        if (!in_array($fromCurrency, $this->config->getSupportedCurrencies())) {
            throw new InvalidInputException("Unsupported currency: $fromCurrency");
        }
        
        if ($fromCurrency === 'EUR') {
            return $amount;
        }
        
        return $amount / $this->exchangeRateService->getRate($fromCurrency);
    }

    /**
     * Convert amount from EUR
     * @throws InvalidInputException
     */
    public function convertFromEur(float $amount, string $toCurrency): float
    {
        if (!in_array($toCurrency, $this->config->getSupportedCurrencies())) {
            throw new InvalidInputException("Unsupported currency: $toCurrency");
        }
        
        if ($toCurrency === 'EUR') {
            return $amount;
        }
        
        return $amount * $this->exchangeRateService->getRate($toCurrency);
    }
}
