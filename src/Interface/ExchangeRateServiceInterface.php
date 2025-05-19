<?php

declare(strict_types=1);

namespace CommissionCalculator\Interface;

use RuntimeException;

interface ExchangeRateServiceInterface
{
    /**
     * Get exchange rate for a currency relative to EUR
     *
     * @param string $currency Currency code (must be one of SUPPORTED_CURRENCIES)
     * @return float Exchange rate
     * @throws RuntimeException If rate cannot be fetched or currency is not supported
     */
    public function getRate(string $currency): float;
}
