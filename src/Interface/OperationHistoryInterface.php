<?php

declare(strict_types=1);

namespace CommissionCalculator\Interface;

use CommissionCalculator\Model\Operation;

interface OperationHistoryInterface
{
    /**
     * Get operations for a user within the same week as the given operation
     * @return array<Operation>
     */
    public function getWeeklyOperations(Operation $operation): array;

    /**
     * Add an operation to the history
     */
    public function addOperation(Operation $operation): void;
} 