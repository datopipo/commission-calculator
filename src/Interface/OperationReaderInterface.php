<?php

declare(strict_types=1);

namespace CommissionCalculator\Interface;

use CommissionCalculator\Exception\InvalidInputException;
use CommissionCalculator\Model\Operation;

interface OperationReaderInterface
{
    /**
     * Read operations from CSV file
     *
     * @param string $filePath Path to CSV file
     * @return array<Operation>
     * @throws InvalidInputException
     */
    public function readFromFile(string $filePath): array;
}
