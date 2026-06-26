# Laravel Persian Core

`zarbinco/laravel-persian-core` is a lightweight Laravel foundation package for Persian text, numbers, mobile, money, and validation utilities.

It gives Laravel applications a small, dependency-light base for common Persian normalization tasks without bundling business-specific features such as gateways, calendars, SMS, PDFs, or admin panels.

## Features

- Persian and Arabic text normalization.
- Persian, Arabic, and English digit conversion.
- Storage, display, and search normalization pipelines.
- Number cleaning, parsing, and formatting.
- Iranian mobile normalization and masking foundation.
- Toman/rial money parsing, formatting, and conversion helpers.
- Laravel validation Rule objects for common Iranian/Persian inputs.
- Publishable config and translation files.
- Artisan install, doctor, and about commands.

## Installation

```bash
composer require zarbinco/laravel-persian-core
```

Laravel package discovery registers the service provider and facade automatically.

## Publishing

Publish the package config:

```bash
php artisan vendor:publish --tag=persian-core-config
```

Publish validation translations:

```bash
php artisan vendor:publish --tag=persian-core-lang
```

Or use the package installer to publish both:

```bash
php artisan persian-core:install
```

Use `--force` when you intentionally want to overwrite previously published files.

## Quick Usage

```php
use Zarbinco\PersianCore\Facades\Persian;

Persian::text('علي كاظمي')->normalize();
// علی کاظمی

Persian::number('۱۲۳٤٥۶')->toEnglish();
// 123456

Persian::clean('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷');
// علی کاظمی شماره 09121234567

Persian::normalize('علي كاظمي شماره 09121234567')->forDisplay();
// علی کاظمی شماره ۰۹۱۲۱۲۳۴۵۶۷

Persian::mobile('۰۹۱۲ ۱۲۳ ۴۵۶۷')->international();
// +989121234567

Persian::money(1250000)->format();
// ۱,۲۵۰,۰۰۰ تومان
```

## Text

Text normalization converts Arabic Yeh and Kaf to Persian Yeh and Kaf, removes Arabic diacritics and tatweel, removes problematic invisible characters while preserving ZWNJ, and normalizes whitespace.

```php
Persian::text('علي  كاظمي')->normalize();
// علی کاظمی

Persian::text('سلام ،  علی  ! خوبی ؟')->forDisplay();
// سلام، علی! خوبی؟

Persian::text('می‌روم')->forSearch();
// می روم
```

`normalize()` and text-only `forStorage()` stay conservative. `forDisplay()` applies display-friendly punctuation cleanup. `forSearch()` applies more aggressive search normalization such as punctuation removal and configurable ZWNJ handling.

## Numbers

The number normalizer focuses on digit conversion. Number parsing helpers clean formatted input for numeric use.

```php
Persian::number('۱۲۳٤٥۶')->toEnglish();
// 123456

Persian::number('123456')->toPersian();
// ۱۲۳۴۵۶

Persian::number('۱,۲۵۰,۰۰۰')->clean();
// 1250000

Persian::number('abc ۱۲۳ def')->digitsOnly();
// 123

Persian::number('۱۲٫۵')->toFloat();
// 12.5

Persian::number('۱۲۳۴۵۶۷')->format();
// ۱,۲۳۴,۵۶۷

Persian::number('۱۲۳۴۵۶۷')->format('en');
// 1,234,567
```

## Normalization Pipeline

`Persian::clean()` is an alias for storage normalization. Storage and display digit output are config-driven.

```php
Persian::normalize('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷')->forStorage();
// علی کاظمی شماره 09121234567

Persian::normalize('علي كاظمي شماره 09121234567')->forDisplay();
// علی کاظمی شماره ۰۹۱۲۱۲۳۴۵۶۷

Persian::normalize('علي مبلغ ۱,۲۵۰,۰۰۰ تومان')->forSearch();
// علی مبلغ 1250000 تومان
```

## Mobile

Mobile helpers normalize Iranian mobile numbers and format them in national or international forms.

```php
Persian::mobile('۰۹۱۲ ۱۲۳ ۴۵۶۷')->normalize();
// 09121234567

Persian::mobile('+989121234567')->national();
// 09121234567

Persian::mobile('09121234567')->international();
// +989121234567

Persian::mobile('09121234567')->e164();
// +989121234567

Persian::mobile('09121234567')->mask();
// 0912***4567
```

The default mask can be changed with `mobile.iran.mask_pattern`. Invalid masks fall back safely to `0912***4567`.

## Money

Money helpers parse and format toman/rial values. They are not accounting, tax, invoice, or payment tools.

```php
Persian::money('۱,۲۵۰,۰۰۰ تومان')->value();
// 1250000

Persian::money('۱۲,۵۰۰,۰۰۰ ریال')->detectedCurrency();
// rial

Persian::money(1250000)->format();
// ۱,۲۵۰,۰۰۰ تومان

Persian::money(1250000)->format('toman', 'en');
// 1,250,000 toman

Persian::money(12500000)->fromRial()->toToman();
// 1250000

Persian::money(1250000)->fromToman()->toRial();
// 12500000
```

The default conversion rate is `1 toman = 10 rial` and can be changed through `money.rial_to_toman_rate`.

## Validation

The package includes Laravel validation Rule objects. Empty values pass by default, so combine these rules with Laravel's `required` rule when a field must be present.

```php
use Zarbinco\PersianCore\Rules\IranianMobile;
use Zarbinco\PersianCore\Rules\IranianNationalCode;
use Zarbinco\PersianCore\Rules\PersianAlpha;

$request->validate([
    'name' => ['required', new PersianAlpha()],
    'mobile' => ['required', new IranianMobile()],
    'national_code' => ['nullable', new IranianNationalCode()],
]);
```

Available rules:

- `PersianText`
- `PersianAlpha`
- `PersianAlphaNum`
- `IranianMobile`
- `IranianNationalCode`
- `IranianPostalCode`
- `IranianSheba`
- `IranianCardNumber`
- `PersianMoneyAmount`

Validation boundaries:

- `IranianMobile` checks normalized Iranian mobile shape, not operator ownership.
- `IranianSheba` checks format and IBAN checksum, not bank account ownership.
- `IranianCardNumber` checks shape and Luhn by default, not card ownership.
- `PersianMoneyAmount` checks shape and parseability, not business min/max rules.

## Artisan Commands

```bash
php artisan persian-core:install
php artisan persian-core:doctor
php artisan persian-core:about
```

`persian-core:doctor` checks common setup and configuration mistakes. `persian-core:about` prints package, environment, config, module, and command information.

## Configuration

The default config is published to `config/persian-core.php`.

```php
'numbers' => [
    'storage_digits' => 'en',
    'display_digits' => 'fa',
    'thousands_separator' => ',',
    'decimal_separator' => '.',
],
```

`numbers.storage_digits` controls storage normalization:

- `en` means `forStorage()` and `clean()` return English digits.
- `fa` means `forStorage()` and `clean()` return Persian digits.

`numbers.display_digits` controls display normalization:

- `fa` means `forDisplay()` returns Persian digits.
- `en` means `forDisplay()` returns English digits.

Defaults are `storage_digits: en` and `display_digits: fa`. Unsupported digit modes fall back safely to those defaults.

Other notable config groups:

- `text`: base text cleanup plus display/search normalization behavior.
- `mobile`: Iranian mobile country code, national prefix, and mask pattern.
- `money`: default currency, labels, display digits, separators, and conversion rate.
- `validation`: validation-rule strictness and empty-value behavior.
- `developer_experience`: reserved developer-experience toggles. String macros are disabled by default.

## Not Included

This core package intentionally does not include:

- Payment gateways or PSP integrations.
- SMS sending.
- Invoice or PDF generation.
- Tax, Modian, or accounting workflows.
- Filament integrations.
- Jalali calendar support.
- Address, province, or city databases.
- Business-specific validation policies.

## Testing

```bash
composer validate --strict
composer install
composer test
composer analyse
composer format -- --test
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for local setup, coding style, and pull request expectations.

## Security

Please see [SECURITY.md](SECURITY.md) for supported versions and vulnerability reporting.

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
