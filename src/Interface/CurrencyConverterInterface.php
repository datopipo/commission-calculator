<?php

declare(strict_types=1);

namespace CommissionCalculator\Interface;

interface CurrencyConverterInterface
{
    /**
     * Convert amount to EUR
     */
    public function convertToEur(float $amount, string $fromCurrency): float;

    /**
     * Convert amount from EUR
     */
    public function convertFromEur(float $amount, string $toCurrency): float;
}
