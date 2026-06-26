# Changelog

All notable changes to `zarbinco/laravel-persian-core` will be documented in this file.

The format is inspired by [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project follows semantic versioning after the first stable release.

## [Unreleased]

- Nothing yet.

## [1.0.0] - 2026-06-25

### Added

- Added Laravel package skeleton, service provider, auto-discovery, facade alias, and publishable config.
- Added Persian text normalization for Arabic Yeh/Kaf conversion, diacritic removal, tatweel removal, invisible-character cleanup, whitespace normalization, and ZWNJ preservation.
- Added Persian number normalization for English, Persian, and Arabic digit conversion.
- Added config-driven storage and display normalization pipeline behavior.
- Added display and search normalization modes.
- Added fluent APIs for text, number, mobile, money, and combined normalization.
- Added number cleaning, parsing, integer/float conversion, digit extraction, and formatting helpers.
- Added Iranian mobile normalization, national/international/E.164 formatting, and configurable masking foundation.
- Added toman/rial money parsing, formatting, label configuration, display digits, and conversion helpers.
- Added Laravel validation Rule objects for Persian text, Persian alpha, Persian alpha-numeric, Iranian mobile, Iranian national code, Iranian postal code, Iranian Sheba, Iranian card number, and Persian money amounts.
- Added English and Persian validation translation files.
- Added package install, doctor, and about Artisan commands.
- Added config validation support used by the doctor command.
- Added PHPUnit, PHPStan/Larastan, Pint, and GitHub Actions workflow.
- Added release-readiness documentation, contribution, support, security, upgrade, and release checklist files.

### Changed

- Improved README documentation for installation, publishing, usage, configuration, validation boundaries, testing, and exclusions.
- Improved package metadata for Packagist readiness.
- Improved CI to run Composer validation, PHPUnit, PHPStan, and Pint across PHP 8.2, 8.3, and 8.4.
- Improved mobile mask config behavior so explicit masks take priority, valid config masks are respected, and invalid config masks fall back safely.
- Improved Sheba validation to require exact normalized length and avoid unsafe auto-padding.

### Fixed

- Fixed the Phase 1 pipeline design gap so number digit config values are used for storage and display normalization.
- Fixed edge-case coverage for null input, Arabic diacritics, tatweel, invisible characters, and ZWNJ preservation.
