# Contributing

Thanks for considering a contribution to `zarbinco/laravel-persian-core`.

## Local Setup

```bash
composer install
```

Run the full validation suite before opening a pull request:

```bash
composer validate --strict
composer test
composer analyse
composer format -- --test
```

Use `composer format` to apply Pint formatting when needed.

## Pull Requests

- Keep changes focused and small enough to review.
- Add or update tests for behavior changes.
- Update README, CHANGELOG, and REVIEW_NOTES when user-facing behavior changes.
- Avoid adding heavy dependencies unless they are clearly justified.
- Keep this package focused on foundation utilities.

## Scope

This package intentionally avoids payment gateways, SMS sending, invoice/PDF generation, Jalali calendar support, Filament support, tax integrations, and business-specific workflows.
