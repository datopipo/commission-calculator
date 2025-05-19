<?php

declare(strict_types=1);

namespace CommissionCalculator\Service;

use CommissionCalculator\Interface\OperationHistoryInterface;
use CommissionCalculator\Model\Operation;
use DateTime;
use Exception;

class OperationHistory implements OperationHistoryInterface
{
    /** @var array<Operation> */
    private array $operations = [];

    /**
     * Get weekly operations for a given operation
     * 
     * @param Operation $operation The operation to get weekly history for
     * @return array<Operation> Array of operations in the same week
     * @throws Exception
     */
    public function getWeeklyOperations(Operation $operation): array
    {
        $operationDate = new DateTime($operation->getDate());
        $operationYear = $operationDate->format('Y');
        $operationWeek = $operationDate->format('W');
        $userId = $operation->getUserId();

        $weeklyOperations = array_filter(
            $this->operations,
            function (Operation $op) use ($userId, $operationYear, $operationWeek) {
                $opDate = new DateTime($op->getDate());
                return $op->getUserId() === $userId &&
                       $op->getUserType() === Operation::PRIVATE &&
                       $op->getOperationType() === Operation::WITHDRAW &&
                       $opDate->format('Y') === $operationYear &&
                       $opDate->format('W') === $operationWeek;
            }
        );

        // Sort operations by date and user ID
        usort($weeklyOperations, function (Operation $a, Operation $b) {
            $dateCompare = strtotime($a->getDate()) <=> strtotime($b->getDate());
            if ($dateCompare !== 0) {
                return $dateCompare;
            }
            return $a->getUserId() <=> $b->getUserId();
        });

        return array_values($weeklyOperations);
    }

    /**
     * Add an operation to history
     */
    public function addOperation(Operation $operation): void
    {
        // Only add if not already in history
        foreach ($this->operations as $op) {
            if ($this->isSameOperation($op, $operation)) {
                return;
            }
        }

        $this->operations[] = $operation;
    }

    /**
     * Compare two operations for equality
     * 
     * @param Operation $a First operation
     * @param Operation $b Second operation
     * @return bool True if operations are equal
     */
    private function isSameOperation(Operation $a, Operation $b): bool
    {
        return $a->getDate() === $b->getDate() &&
               $a->getUserId() === $b->getUserId() &&
               $a->getUserType() === $b->getUserType() &&
               $a->getOperationType() === $b->getOperationType() &&
               $a->getAmount() === $b->getAmount() &&
               $a->getCurrency() === $b->getCurrency();
    }
}
