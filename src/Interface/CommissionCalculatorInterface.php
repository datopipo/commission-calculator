<?php

declare(strict_types=1);

namespace CommissionCalculator\Interface;

use CommissionCalculator\Model\Operation;

interface CommissionCalculatorInterface
{
    /**
     * Calculate commission for a single operation
     */
    public function calculateCommission(Operation $operation): float;
}
