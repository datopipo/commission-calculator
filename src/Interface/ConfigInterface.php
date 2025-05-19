<?php

declare(strict_types=1);

namespace CommissionCalculator\Interface;

interface ConfigInterface
{
    /**
     * Get a configuration value
     *
     * @param string $key Configuration key
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Check if a configuration key exists
     *
     * @param string $key Configuration key
     * @return bool True if key exists
     */
    public function has(string $key): bool;

    /**
     * Set a configuration value
     *
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     */
    public function set(string $key, mixed $value): void;
} 