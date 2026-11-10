# Upgrade Guide

## Compatibility

The package requires PHP `^8.2` and supports Illuminate components `^12.0` and `^13.0`, subject to each framework version's PHP requirements:

| Laravel | Supported PHP versions |
| --- | --- |
| 12 | 8.2, 8.3, 8.4, 8.5 |
| 13 | 8.3, 8.4, 8.5 |

## Validation behavior

Validation rules allow optional empty values by default. Add Laravel's `required` rule when a field must be present. Money validation is strict by default and can use relaxed extraction when explicitly configured.

Review the validation settings in `config/persian-core.php` when upgrading if your application relies on custom strictness or empty-value behavior.

## Extending package services

Applications may replace documented package contracts through Laravel's container. Custom implementations should preserve the inputs, return types, and behavior defined by the contract they implement.

The package's bank metadata is local lookup data only. It does not enable live bank verification, and unknown bank, card, or Sheba lookups continue to return `null`.
