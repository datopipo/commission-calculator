<?php

use CommissionCalculator\Application;
use CommissionCalculator\Config\Config;
use CommissionCalculator\Config\AppConfig;
use CommissionCalculator\Service\CommissionCalculator;
use CommissionCalculator\Service\CurrencyConverter;
use CommissionCalculator\Service\ExchangeRateService;
use CommissionCalculator\Service\OperationHistory;
use CommissionCalculator\Service\OperationParser;
use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$config = new Config();
$appConfig = new AppConfig($config);
$exchangeRateService = new ExchangeRateService($appConfig);
$currencyConverter = new CurrencyConverter($exchangeRateService, $appConfig);
$operationHistory = new OperationHistory();
$commissionCalculator = new CommissionCalculator(
    $appConfig,
    $currencyConverter,
    $operationHistory
);
$operationParser = new OperationParser($appConfig);
$app = new Application(
    $operationParser,
    $commissionCalculator,
    $operationHistory
);
return $app;