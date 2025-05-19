<?php

declare(strict_types=1);

namespace CommissionCalculator\Config;

use CommissionCalculator\Interface\AppConfigInterface;
use CommissionCalculator\Interface\ConfigInterface;
use RuntimeException;

/**
 * Application Configuration
 * 
 * Handles all application configuration including:
 * - Supported currencies
 * - Exchange rates
 * - Commission rates and limits
 * - Currency decimal places
 */
class AppConfig implements AppConfigInterface
{
    private const DEFAULT_DECIMAL_PLACES = 2;

    public function __construct(
        private readonly ConfigInterface $config
    ) {
    }

    /**
     * {@inheritDoc}
     * @throws RuntimeException If no currencies are configured or format is invalid
     */
    public function getSupportedCurrencies(): array
    {
        $currencies = $this->config->get('SUPPORTED_CURRENCIES');
        if (empty($currencies)) {
            return [];
        }

        return explode(',', $currencies);
    }

    /**
     * {@inheritDoc}
     * @throws RuntimeException If rates are not configured or format is invalid
     */
    public function getFixedRates(): array
    {
        $fixedRates = $this->config->get('FIXED_RATES');
        if (empty($fixedRates)) {
            return [];
        }

        return json_decode($fixedRates, true) ?: [];
    }

    /**
     * {@inheritDoc}
     * @throws RuntimeException If commission configuration is invalid
     */
    public function getCommissionConfiguration(): array
    {
        return [
            'deposit' => [
                'rate' => (float)$this->config->get('COMMISSION_DEPOSIT_RATE', 0.0003),
            ],
            'withdraw' => [
                'private' => [
                    'rate' => (float)$this->config->get('COMMISSION_WITHDRAW_PRIVATE_RATE', 0.003),
                    'free_amount' => (float)$this->config->get('COMMISSION_WITHDRAW_PRIVATE_FREE_AMOUNT', 1000.00),
                    'free_operations' => (int)$this->config->get('COMMISSION_WITHDRAW_PRIVATE_FREE_OPERATIONS', 3),
                ],
                'business' => [
                    'rate' => (float)$this->config->get('COMMISSION_WITHDRAW_BUSINESS_RATE', 0.005),
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDecimalPlacesForCurrency(string $currency): int
    {
        return $currency === 'JPY' ? 0 : self::DEFAULT_DECIMAL_PLACES;
    }
} 