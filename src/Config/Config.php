<?php

declare(strict_types=1);

namespace CommissionCalculator\Config;

use CommissionCalculator\Interface\ConfigInterface;

class Config implements ConfigInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_ENV[$key]);
    }

    public function set(string $key, mixed $value): void
    {
        $_ENV[$key] = $value;
    }
} 