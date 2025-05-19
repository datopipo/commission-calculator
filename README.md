# Commission Calculator

A PHP application that calculates commission fees for financial operations according to configurable business rules.

## Features

- Calculates commission for deposits and withdrawals
- Supports multiple currencies (EUR, USD, JPY by default)
- Handles private and business users with different rules
- Weekly free limits for private withdrawals
- Rounds results according to currency rules
- Easily extensible for new currencies or commission rules

## Requirements

- PHP 8.1+
- Composer

## Installation

1. Clone the repository:
    ```bash
    git clone <repository-url>
    cd <project-directory>
    ```

2. Install dependencies:
```bash
    composer install
    ```

## Configuration

Set the following environment variables (or add them to a `.env` file):

```env
SUPPORTED_CURRENCIES=EUR,USD,JPY
FIXED_RATES={"EUR":1,"USD":1.1497,"JPY":129.53}
COMMISSION_DEPOSIT_RATE=0.0003
COMMISSION_WITHDRAW_PRIVATE_RATE=0.003
COMMISSION_WITHDRAW_BUSINESS_RATE=0.005
COMMISSION_WITHDRAW_PRIVATE_FREE_AMOUNT=1000.00
COMMISSION_WITHDRAW_PRIVATE_FREE_OPERATIONS=3
```

These variables control supported currencies, exchange rates, and commission rules.  
**No code changes are needed to add new currencies or update commission rules—just update these variables.**

## Usage

Prepare your input CSV file (see format below), then run:

```bash
php index.php input.csv
```

### Input CSV Format

Each line should contain:

```
Date,User ID,User Type,Operation Type,Amount,Currency
```

Example:

```
2014-12-31,4,private,withdraw,1200.00,EUR
2015-01-01,4,private,withdraw,1000.00,EUR
```

## Commission Rules

- **Deposit:** 0.03% of amount
- **Business Withdraw:** 0.5% of amount
- **Private Withdraw:** 0.3% of amount, with free conditions:
  - First 3 operations per week
  - Up to 1000 EUR per week (converted from other currencies if needed)
- **Rounding:** Always round up to the smallest currency unit (2 decimals for EUR/USD, 0 for JPY)

## Testing

Run the automation test:

```bash
composer test
```
or
```bash
./vendor/bin/phpunit
```

This will verify the application against the provided sample input and expected output.

## Project Structure

```
src/
├── Config/           # Configuration classes
├── Exception/        # Custom exceptions
├── Interface/        # Service interfaces
├── Model/            # Domain models
└── Service/          # Business logic services
tests/                # Automation test
```

## Extending

- To add a new currency, update the environment variables (no code changes needed).
- To change commission rules, update the environment variables.

---

**No external infrastructure is required. All calculations are performed in memory.**

---

If you have any questions or want to extend the system, see the inline code comments for guidance.
