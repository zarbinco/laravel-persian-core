# Contributing

## Local setup

From the repository root, install the development dependencies.

```bash
composer install
```

Before opening a pull request, run these checks.

```bash
composer validate --strict
composer test
composer analyse
composer format -- --test
```

`composer lint` combines Composer validation, static analysis, and tests. Use `composer format` to apply Pint formatting when needed.

## Pull requests

- Keep changes focused and small enough to review.
- Add or update tests for behavior changes.
- Update the relevant examples in the [English README](README.md) and [Persian README](README.fa.md) when their documented behavior changes. Record notable changes in the [changelog](CHANGELOG.md).

## Scope

This package intentionally avoids payment gateways, SMS sending, invoice/PDF generation, Jalali calendar support, Filament support, tax integrations, and business-specific workflows.
