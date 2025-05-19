<?php

declare(strict_types=1);

namespace CommissionCalculator\Interface;

use RuntimeException;

interface AppConfigInterface
{
    /**
     * Get supported currencies
     * @return array<string> List of supported currency codes
     * @throws RuntimeException If no currencies are configured
     */
    public function getSupportedCurrencies(): array;

    /**
     * Get fixed exchange rates
     * @return array<string,float> Map of currency codes to exchange rates
     * @throws RuntimeException If rates are not configured
     */
    public function getFixedRates(): array;

    /**
     * Get commission configuration
     * @return array Commission rates and limits
     * @throws RuntimeException If commission configuration is invalid
     */
    public function getCommissionConfiguration(): array;

    /**
     * Get decimal places for a currency
     * @param string $currency Currency code
     * @return int Number of decimal places (0 for JPY, 2 for others)
     */
    public function getDecimalPlacesForCurrency(string $currency): int;
} 