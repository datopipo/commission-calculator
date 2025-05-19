<?php

declare(strict_types=1);

namespace CommissionCalculator;

use CommissionCalculator\Exception\InvalidInputException;
use CommissionCalculator\Interface\CommissionCalculatorInterface;
use CommissionCalculator\Interface\OperationHistoryInterface;
use CommissionCalculator\Interface\OperationReaderInterface;

class Application
{
    public function __construct(
        private readonly OperationReaderInterface $operationReader,
        private readonly CommissionCalculatorInterface $commissionCalculator,
        private readonly OperationHistoryInterface $operationHistory
    ) {
    }

    /**
     * Run the application with the given input file
     *
     * @param string $inputFile Path to the input CSV file
     * @throws InvalidInputException
     */
    public function run(string $inputFile): void
    {
        $operations = $this->operationReader->readFromFile($inputFile);

        foreach ($operations as $operation) {
            $commission = $this->commissionCalculator->calculateCommission($operation);
            $this->operationHistory->addOperation($operation);
            $this->formatAndOutputCommission($commission, $operation->getCurrency());
        }
    }

    private function formatAndOutputCommission(float $amount, string $currency): void
    {
        $decimals = match ($currency) {
            'JPY' => 0,
            default => 2
        };
        
        // Format with specified decimals
        $formatted = number_format($amount, $decimals, '.', '');
        
        echo $formatted . "\n";
    }
}
