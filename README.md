# Laravel Persian Core

[![Tests](https://github.com/zarbinco/laravel-persian-core/actions/workflows/tests.yml/badge.svg)](https://github.com/zarbinco/laravel-persian-core/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/zarbinco/laravel-persian-core.svg)](https://packagist.org/packages/zarbinco/laravel-persian-core)
[![Total Downloads](https://img.shields.io/packagist/dt/zarbinco/laravel-persian-core.svg)](https://packagist.org/packages/zarbinco/laravel-persian-core)
[![License](https://img.shields.io/packagist/l/zarbinco/laravel-persian-core.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/packagist/php-v/zarbinco/laravel-persian-core.svg)](composer.json)

`zarbinco/laravel-persian-core` is a lightweight Persian/Iranian utility foundation for Laravel applications.

It handles text normalization, digit conversion, mobile normalization, money helpers, validation rules, and offline bank/card/Sheba detection. It gives Laravel applications a small, dependency-light base for common Persian and Iranian input handling without bundling business-specific features.

[Read this README in Persian](README.fa.md).

## Features

- Persian and Arabic text normalization.
- Persian, Arabic, and English digit conversion.
- Storage, display, and search normalization pipelines.
- First-class Persian search normalization for indexing and query cleanup.
- Number cleaning, parsing, and formatting.
- Iranian mobile normalization and masking foundation.
- Toman/rial money parsing, formatting, and conversion helpers.
- Laravel validation Rule objects for common Iranian/Persian inputs.
- Offline best-effort Iranian bank detection from card BIN/IIN values and Sheba bank codes.
- Publishable config and translation files.
- Artisan install, doctor, and about commands.

## What This Package Is Not

This core package intentionally does not include:

- Payment gateways, PSP integrations, or payment processing.
- Banking verification, ownership checks, or live account/card status checks.
- SMS sending.
- Jalali calendar support.
- Invoice or PDF generation.
- Admin panels or Filament integrations.
- Tax, Modian, accounting, or bookkeeping workflows.
- Full-text search engines, ranking systems, stemming, fuzzy matching, or Scout/database integrations.
- Address, province, or city databases.
- Business-specific validation policies.

## Validation vs Normalization

Normalizers are designed for cleanup, formatting, conversion, and predictable storage/display/search output. Some normalizers may be permissive because they are useful for extracting or cleaning messy user input.

Validators are designed for Laravel validation. They validate shape, required structure, and checksums where applicable. They do not prove real-world ownership, account existence, operator ownership, card status, or live banking status.

Use normalizers when you need normalized output. Use validation rules when you need to reject invalid input. Combine validation rules with Laravel's `required` rule when a field must be present.

## Bank Detection Boundaries

Bank/card/Sheba detection is offline and best-effort. It uses local metadata for Iranian card BIN/IIN values and Sheba bank codes, and unknown banks return `null`.

Bank detection does not prove:

- Account ownership.
- Account existence.
- Card ownership.
- Card status.
- Card-to-account convertibility.
- Whether a bank still actively issues or accepts a specific identifier.

Use `IranianCardNumber` and `IranianSheba` validation rules when validation is needed. Even then, validation is limited to shape and checksum checks where applicable, not live banking verification.

## Compatibility

Compatibility is based on `composer.json`:

- PHP `^8.2`.
- Laravel components `^11.0`, `^12.0`, or `^13.0`.
- Package discovery registers the service provider and facade automatically.
- License: MIT.

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

## Search Normalization

Search normalization prepares deterministic text for indexing and user-query matching. It normalizes Persian/Arabic letter variants, removes diacritics and tatweel, handles ZWNJ according to config, converts Persian and Arabic digits to English, removes punctuation by default, and collapses digit groups.

```php
Persian::search('كیكِ شکلاتي')->normalize();
// کیک شکلاتی

Persian::search('آب‌میوه سن‌ایچ ۱۲,۵۰۰ تومان!')->normalize();
// اب میوه سن ایچ 12500 تومان

Persian::search('آب‌میوه سن‌ایچ')->tokens();
// ['اب', 'میوه', 'سن', 'ایچ']
```

`Persian::searchable($value)` is kept as a direct string helper and uses the same search normalizer:

```php
$model->searchable_name = Persian::search($model->name)->normalize();
$model->save();

$query = Persian::search($request->input('q'))->normalize();

Product::query()
    ->where('searchable_name', 'like', "%{$query}%")
    ->get();
```

This is normalization for search/indexing. It is not a full-text search engine, stemming system, transliteration layer, fuzzy matcher, Scout integration, or database-specific ranking tool.

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

## Bank Detection

Bank detection provides best-effort metadata from Iranian card BIN/IIN values and Sheba bank codes. It is offline-only, does not call external services, and unknown banks return `null`.

```php
$bank = Persian::card('6037991234567890')->bank();

$bank?->toArray();
// [
//     'slug' => 'melli',
//     'name' => 'Bank Melli Iran',
//     'name_fa' => 'بانک ملی ایران',
//     'card_bins' => ['603799'],
//     'sheba_codes' => ['017'],
// ]

Persian::card('6037991234567890')->bankSlug();
// melli

Persian::sheba('IR170170000000000000000000')->bankNameFa();
// بانک ملی ایران
```

Direct helpers are available when you only need the bank object:

```php
Persian::bankFromCard('۶۰۳۷۹۹ ۱۲۳۴ ۵۶۷۸ ۹۰۱۲')?->slug();
// melli

Persian::bankFromSheba('ir170170000000000000000000')?->name();
// Bank Melli Iran
```

Bank detection is not validation and does not prove ownership, existence, account status, or card/account convertibility. Use `IranianCardNumber` and `IranianSheba` validation rules when validation is needed.

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

### Strict Validation Mode

Validators are strict by default. Strict mode rejects values that only contain a valid value inside surrounding text, while normalizers remain permissive for extraction and cleanup use cases.

```php
'validation' => [
    'strict' => true,
    'empty_values_pass' => true,
],
```

For legacy behavior, disable strict validation globally or per rule:

```php
config(['persian-core.validation.strict' => false]);

new IranianMobile(strict: false);
new IranianCardNumber(strict: true);
```

Strict validation still accepts Persian, Arabic, and English digits plus common separators for full values such as `0912-123-4567`, `6037 9900 0000 0006`, `IR18 0100 0000 0000 0000 0000 00`, and `۱,۲۵۰,۰۰۰ تومان`.

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
- `banks`: bank detection behavior documentation toggles.
- `bank_data`: informational metadata for the bundled offline bank dataset.
- `validation`: validation-rule strictness and empty-value behavior.
- `developer_experience`: reserved developer-experience toggles. String macros are disabled by default.

## Extending / Overriding Services

Core services are bound to small contracts so applications can override implementation details through Laravel's container:

```php
use App\Support\CustomTextNormalizer;
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;

$this->app->bind(PersianTextNormalizerContract::class, CustomTextNormalizer::class);
```

Custom implementations should preserve the documented behavior and return types expected by the contract. Bank detection remains offline metadata-based unless your application explicitly replaces it with its own service.

## Testing / Quality

Available Composer quality commands:

```bash
composer validate --strict
composer test
composer analyse
composer format -- --test
composer lint
```

`composer test` runs PHPUnit. `composer analyse` runs PHPStan/Larastan. `composer format -- --test` runs Pint in check mode. `composer lint` runs Composer validation, PHPStan, and PHPUnit.

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for local setup, coding style, and pull request expectations.

## Security

Please see [SECURITY.md](SECURITY.md) for supported versions and vulnerability reporting.

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
