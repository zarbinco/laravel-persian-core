# Release Gate

This document is a release candidate checklist for `zarbinco/laravel-persian-core`. It does not claim that a release has happened and does not require a specific next tag.

## Release Readiness Checklist

- Confirm the working tree contains only intended release changes.
- Confirm `composer.json` package metadata, PHP constraint, Laravel component constraints, support URLs, and license are correct.
- Confirm `README.md`, `README.fa.md`, `CHANGELOG.md`, `UPGRADE.md`, `SECURITY.md`, and `SUPPORT.md` do not claim an unpublished release.
- Confirm README badges point to existing resources, especially `.github/workflows/tests.yml`.
- Confirm `config/persian-core.php` contains only backward-compatible defaults.
- Confirm all public Facade and Manager APIs documented in README still work.
- Confirm validation rules keep their documented strict/empty-value behavior.
- Confirm offline bank/card/Sheba detection still returns documented best-effort metadata or `null`.
- Confirm GitHub repository About and Topics are set manually from `docs/github-metadata.md`.

## Required Commands Before Tagging

Run these from the package root:

```bash
composer validate --strict
composer run-script --list
composer test
composer analyse
composer format -- --test
```

Run dependency-mode checks when practical:

```bash
composer update --prefer-lowest --prefer-stable --no-interaction --no-progress
composer test
composer analyse
composer format -- --test
composer update --prefer-stable --no-interaction --no-progress
```

If the package is intentionally kept without `composer.lock`, remove any generated lock file after local dependency checks.

## Manual GitHub Metadata Checklist

Set these manually in the GitHub repository sidebar:

- About text from `docs/github-metadata.md`.
- Topics from `docs/github-metadata.md`.
- Repository homepage or package URL if desired.
- Security policy visibility.
- Issue and pull request settings.

## Packagist And GitHub Release Checklist

Before tagging:

- Confirm the next version number.
- Confirm `CHANGELOG.md` still has an `[Unreleased]` section.
- Move only the intended unreleased entries into a release heading during the actual release commit.
- Create the git tag only after review.
- Push the tag to GitHub.
- Confirm Packagist receives the tag.
- Confirm the GitHub Actions workflow passes for the tag.
- Draft GitHub release notes from the changelog.

## Compatibility Matrix Summary

The package constraints currently target:

| Runtime | Supported Range |
| --- | --- |
| PHP | `^8.2` |
| Laravel components | `^11.0`, `^12.0`, `^13.0` |
| CI PHP versions | `8.2`, `8.3`, `8.4` |
| CI dependency modes | highest, lowest |

Do not advertise a Laravel version as supported unless dependency resolution and CI pass for it.

## Public API Stability Checklist

Before tagging, confirm these remain available:

```php
Persian::normalize($value);
Persian::clean($value);
Persian::searchable($value);
Persian::search($value);
Persian::text($value);
Persian::number($value);
Persian::mobile($value);
Persian::money($value);
Persian::card($value);
Persian::sheba($value);
Persian::bankFromCard($value);
Persian::bankFromSheba($value);
```

Also confirm the documented validation rules and extension contracts resolve as expected.

## Known Non-Goals

This package is not:

- A payment gateway or PSP integration.
- A live banking verification service.
- An SMS package.
- A Jalali calendar package.
- A PDF or invoice generator.
- An admin panel.
- An accounting, tax, or Modian workflow package.
- A full-text search engine.

## Bank Detection Disclaimer

Bank/card/Sheba detection is offline and best-effort. It does not prove account ownership, account existence, card ownership, card status, card-to-account convertibility, or live banking status.

## Consumer Smoke-Test Instructions

Run smoke tests in fresh Laravel applications outside this repository.

### Laravel 11

```bash
composer create-project laravel/laravel:^11.0 persian-core-smoke-11
cd persian-core-smoke-11
composer require zarbinco/laravel-persian-core:dev-main
php artisan vendor:publish --tag=persian-core-config
php artisan vendor:publish --tag=persian-core-lang
php artisan about
php artisan tinker
```

Inside Tinker:

```php
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;
use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Rules\IranianMobile;

Persian::text('علي كاظمي')->normalize();
Persian::number('۱۲۳٤٥۶')->toEnglish();
Persian::mobile('۰۹۱۲ ۱۲۳ ۴۵۶۷')->international();
Persian::bankFromCard('6037991234567890')?->slug();
app(PersianTextNormalizerContract::class)->normalize('علي');
validator(['mobile' => '09121234567'], ['mobile' => [new IranianMobile]])->passes();
```

### Laravel 12

```bash
composer create-project laravel/laravel:^12.0 persian-core-smoke-12
cd persian-core-smoke-12
composer require zarbinco/laravel-persian-core:dev-main
php artisan vendor:publish --tag=persian-core-config
php artisan vendor:publish --tag=persian-core-lang
php artisan tinker
```

Repeat the Tinker checks from the Laravel 11 smoke test.

### Laravel 13

Laravel 13 is included in the current Composer constraints. Test it only if dependency resolution supports it in your environment:

```bash
composer create-project laravel/laravel:^13.0 persian-core-smoke-13
cd persian-core-smoke-13
composer require zarbinco/laravel-persian-core:dev-main
php artisan vendor:publish --tag=persian-core-config
php artisan vendor:publish --tag=persian-core-lang
php artisan tinker
```

Repeat the Tinker checks from the Laravel 11 smoke test.

## Suggested Versioning Options

The maintainer should choose the next public tag based on release intent:

- `v0.2.0` for a pre-stable minor release with documentation, CI, contracts, and release-gate improvements.
- `v0.3.0` if the maintainer wants one more pre-stable cycle before a stable tag.
- `v1.0.0` only if the maintainer decides the public API is stable and ready for a first stable release.

Do not tag `v1.0.0` unless that stable-release decision has been made explicitly.
