# Upgrade Guide

## Current Unreleased Review

The Phase 1-4 documentation, CI, tests, contracts, config metadata, and release-gate changes are intended to be backward-compatible.

Within Phases 1-4, no validation behavior was intentionally changed. However, the broader current `[Unreleased]` changelog includes validation hardening and strict validation mode entries. Before tagging, review those entries and document any stricter validation impact clearly in the release notes.

Existing Facade and Manager APIs remain supported, including:

```php
Persian::normalize($value);
Persian::clean($value);
Persian::searchable($value);
Persian::bankFromCard($value);
Persian::bankFromSheba($value);
```

Consumers should review the README for new optional extension contracts and informational bank data metadata.

The optional `bank_data` config section is metadata only. It does not enable live bank verification and does not change the documented `null` fallback for unknown bank/card/Sheba detection.

## Custom Contract Implementations

Applications may override package contracts through Laravel's container. Custom implementations should preserve the documented behavior and return types expected by the contract they replace.

## Requirements

- PHP `^8.2`.
- Laravel components `^11.0`, `^12.0`, or `^13.0`.
