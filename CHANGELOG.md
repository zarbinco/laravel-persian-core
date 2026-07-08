# Changelog

All notable changes to `zarbinco/laravel-persian-core` will be documented in this file.

The format is inspired by [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project follows semantic versioning after the first stable release.

## [Unreleased]

### Added

- Added strict validation mode with `validation.strict` enabled by default and per-rule constructor overrides.
- Added a first-class Persian search normalizer with `Persian::search($value)->normalize()`, tokenization, and centralized `Persian::searchable($value)` behavior.
- Added offline best-effort Iranian bank detection from card BIN/IIN values and Sheba bank codes.

### Changed

- Hardened Iranian mobile, national code, postal code, card number, Sheba, and Persian money amount rules so strict validation rejects embedded garbage text while preserving normalizer behavior.
- Refactored search normalization paths to share deterministic text, digit, punctuation, and digit-group normalization.

### Documentation

- Added a Persian README.
- Improved README structure, compatibility details, package boundaries, and testing/quality guidance.
- Added GitHub metadata recommendations for repository About and Topics.
- Added README badges for tests, Packagist version/downloads, license, and PHP requirements.

### Testing

- Added edge-case coverage for Persian text normalization, digit conversion, search normalization, mobile normalization, money formatting, validation rules, and offline bank detection.

### CI

- Expanded the GitHub Actions compatibility matrix to run against highest and lowest dependency sets on PHP 8.2, 8.3, and 8.4.
- Added Composer dependency caching and aligned CI test, analysis, and style checks with the existing Composer scripts.

## [0.1.0] - 2026-06-26

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
