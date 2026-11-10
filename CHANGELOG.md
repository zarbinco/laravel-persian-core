# Changelog

## Unreleased

### Removed

- Dropped Laravel 11 support. The package now targets Laravel 12 on PHP 8.2–8.5 and Laravel 13 on PHP 8.3–8.5.

### Changed

- When Iranian BIN validation is enabled, `IranianCardNumber` now uses the same bundled BIN list as the default offline bank lookup. The accepted prefixes have changed. The default Luhn check still applies.
- CI now checks the Laravel 12 and 13 dependency sets explicitly and verifies production installs in fresh applications.

### Fixed

- Money parsing rejects nonzero fractions, malformed grouping, multiple amounts and unsupported Unicode digits. It still accepts whole amounts written with a zero-only fractional part, such as `۱۲٫۰۰`.
- Currency detection matches whole labels and rejects inputs with more than one currency label.
- `Persian::money($input)->value()` and toman-to-rial conversion return `null` when the result falls outside PHP's integer range.
- Search normalization keeps adjacent numbers separate, including numbers separated by whitespace or operators. Correctly grouped values such as `۱۲٬۵۰۰` still normalize to `12500`.

## [0.2.0] - 2026-07-09

### Added

- Added strict validation by default, with a configuration setting and per-rule overrides. Strict rules reject values such as a mobile number embedded in surrounding text.
- Added `Persian::search()` for search normalization and tokenization. `Persian::searchable()` and the normalization pipeline share the same search behavior.
- Added offline Iranian bank lookup by card BIN/IIN and Sheba bank code.
- Added container contracts for replacing normalizers, formatters and bank lookup, and configuration for bank data metadata.
- Added a Persian README and expanded regression coverage for the documented public API.

### Changed

- Expanded CI to exercise highest and lowest dependency versions on PHP 8.2, 8.3 and 8.4.

## [0.1.0] - 2026-06-26

### Added

- Added Persian text and digit normalization, including storage, display and search output.
- Added number parsing and formatting, Iranian mobile formatting and masking, and toman/rial parsing, formatting and conversion.
- Added Laravel validation rules for Persian text and common Iranian identifiers, with English and Persian messages.
- Added Laravel package discovery, the `Persian` facade, publishable configuration, and the `persian-core:install`, `persian-core:doctor` and `persian-core:about` commands.

### Fixed

- Applied the configured digit style to storage and display normalization.
- Required the exact normalized length for Sheba validation, without padding short inputs.
- Made an explicit mobile mask take precedence over the configured mask and restored the default when the configuration is invalid.
- Preserved zero-width non-joiners during basic text normalization.

[0.2.0]: https://github.com/zarbinco/laravel-persian-core/releases/tag/v0.2.0
[0.1.0]: https://github.com/zarbinco/laravel-persian-core/releases/tag/v0.1.0
