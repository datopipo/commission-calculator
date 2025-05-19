<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use CommissionCalculator\Exception\InvalidInputException;
use CommissionCalculator\Exception\FileNotFoundException;

$app = require_once  __DIR__ . '/bootstrap/app.php';

try {
    if ($argc !== 2) {
        throw new InvalidInputException('Please provide input CSV file path as argument');
    }

    $inputFile = $argv[1];
    if (!file_exists($inputFile)) {
        throw new FileNotFoundException("Input file not found: $inputFile");
    }


    $app->run($inputFile);
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
