<?php

declare(strict_types=1);

namespace CommissionCalculator\Service;

use CommissionCalculator\Interface\AppConfigInterface;
use CommissionCalculator\Interface\OperationReaderInterface;
use CommissionCalculator\Model\Operation;
use CommissionCalculator\Exception\InvalidInputException;
use DateTime;
use InvalidArgumentException;

class OperationParser implements OperationReaderInterface
{
    private const REQUIRED_COLUMNS = 6;
    private const DATE_FORMAT = 'Y-m-d';
    private const CSV_DELIMITER = ',';
    private const CSV_ENCLOSURE = '"';
    private const CSV_ESCAPE = '\\';

    public function __construct(
        private readonly AppConfigInterface $config,
    ) {}

    /**
     * Parse operations from a CSV file
     *
     * @param string $filePath Path to the CSV file
     * @return array<Operation>
     * @throws InvalidInputException
     */
    public function readFromFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new InvalidInputException("File not found: $filePath");
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new InvalidInputException("Could not open file: $filePath");
        }

        try {
            return $this->parseOperations($handle);
        } finally {
            fclose($handle);
        }
    }

    /**
     * Parse operations from the CSV file handle
     *
     * @param resource $handle File handle
     * @return array<Operation>
     * @throws InvalidInputException
     */
    private function parseOperations($handle): array
    {
        $operations = [];
        $lineNumber = 0;

        while (($data = fgetcsv($handle, 0, self::CSV_DELIMITER, self::CSV_ENCLOSURE, self::CSV_ESCAPE)) !== false) {
            $lineNumber++;

            if (empty($data)) {
                continue;
            }

            if ($lineNumber === 1 && isset($data[0]) && strtolower($data[0]) === 'date') {
                continue;
            }

            if (count($data) !== self::REQUIRED_COLUMNS) {
                continue;
            }

            $operations[] = $this->createOperation($data);
        }

        if (empty($operations)) {
            throw new InvalidInputException("No valid operations found in CSV file");
        }

        return $operations;
    }

    /**
     * Create an Operation object from CSV data
     *
     * @param array $data CSV line data
     * @return Operation
     * @throws InvalidInputException
     */
    private function createOperation(array $data): Operation
    {
        [$dateStr, $userId, $userType, $operationType, $amount, $currency] = $data;

        try {
            $date = DateTime::createFromFormat(self::DATE_FORMAT, $dateStr);
            if ($date === false) {
                throw new InvalidInputException("Invalid date: $dateStr");
            }

            return new Operation(
                $this->config,
                $date,
                (int)$userId,
                $userType,
                $operationType,
                (float)$amount,
                $currency
            );
        } catch (InvalidArgumentException $e) {
            throw new InvalidInputException("Invalid operation data: " . $e->getMessage());
        }
    }
}