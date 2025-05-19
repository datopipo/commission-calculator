<?php

declare(strict_types=1);

namespace CommissionCalculator\Model;

use CommissionCalculator\Interface\AppConfigInterface;
use DateTime;
use InvalidArgumentException;

class Operation
{
    public const PRIVATE = 'private';
    public const BUSINESS = 'business';
    public const DEPOSIT = 'deposit';
    public const WITHDRAW = 'withdraw';

    private const VALID_USER_TYPES = [self::PRIVATE, self::BUSINESS];
    private const VALID_OPERATION_TYPES = [self::DEPOSIT, self::WITHDRAW];
    
    public function __construct(
        private readonly AppConfigInterface $config,
        private readonly DateTime $date,
        private readonly int $userId,
        private readonly string $userType,
        private readonly string $operationType,
        private readonly float $amount,
        private readonly string $currency
    ) {
        $this->validateDate();
        $this->validateUserId();
        $this->validateUserType();
        $this->validateOperationType();
        $this->validateAmount();
        $this->validateCurrency();
    }

    private function validateDate(): void
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->date->format('Y-m-d'))) {
            throw new InvalidArgumentException("Invalid date format: {$this->date->format('Y-m-d')}. Expected format: YYYY-MM-DD");
        }
    }

    private function validateUserId(): void
    {
        if ($this->userId <= 0) {
            throw new InvalidArgumentException("User ID must be positive: $this->userId");
        }
    }

    private function validateUserType(): void
    {
        if (!in_array($this->userType, self::VALID_USER_TYPES, true)) {
            throw new InvalidArgumentException("Invalid user type: $this->userType");
        }
    }

    private function validateOperationType(): void
    {
        if (!in_array($this->operationType, self::VALID_OPERATION_TYPES, true)) throw new InvalidArgumentException("Invalid operation type: $this->operationType");
    }

    private function validateAmount(): void
    {
        if ($this->amount < 0) {
            throw new InvalidArgumentException("Amount cannot be negative: $this->amount");
        }
    }

    private function validateCurrency(): void
    {
        if (!in_array($this->currency, $this->config->getSupportedCurrencies(), true)) {
            throw new InvalidArgumentException("Invalid currency: $this->currency");
        }
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
