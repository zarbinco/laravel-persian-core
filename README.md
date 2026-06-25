# Laravel Persian Core

`zarbinco/laravel-persian-core` is a small Laravel package for Persian text and number normalization.

Phase 1 focuses only on a stable foundation: text character cleanup, digit conversion, a combined storage/display pipeline, a fluent API, and a Laravel facade.

## Installation

```bash
composer require zarbinco/laravel-persian-core
```

## Publish Config

```bash
php artisan vendor:publish --tag=persian-core-config
```

## Configuration

The default digit configuration is:

```php
'numbers' => [
    'storage_digits' => 'en',
    'display_digits' => 'fa',
],
```

`numbers.storage_digits` controls the output of storage normalization:

- `en` means `forStorage()` and `clean()` return English digits.
- `fa` means `forStorage()` and `clean()` return Persian digits.

`numbers.display_digits` controls the output of display normalization:

- `fa` means `forDisplay()` returns Persian digits.
- `en` means `forDisplay()` returns English digits.

## Usage

```php
use Zarbinco\PersianCore\Facades\Persian;

Persian::text('علي كاظمي')->normalize();
// علی کاظمی

Persian::number('۱۲۳٤٥۶')->toEnglish();
// 123456

Persian::number('123456')->toPersian();
// ۱۲۳۴۵۶

Persian::clean('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷');
// علی کاظمی شماره 09121234567

Persian::normalize('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷')->forStorage();
// علی کاظمی شماره 09121234567

Persian::normalize('علي كاظمي شماره 09121234567')->forDisplay();
// علی کاظمی شماره ۰۹۱۲۱۲۳۴۵۶۷
```

## Normalization Modes

### Text normalization

Text normalization fixes Persian and Arabic text variants without changing digits. It converts Arabic Yeh and Kaf to Persian Yeh and Kaf, removes Arabic diacritics and tatweel, removes problematic invisible characters while preserving ZWNJ, and normalizes whitespace.

```php
Persian::text('علي ۱۲۳')->normalize();
// علی ۱۲۳
```

### Number normalization

Number normalization only changes digits. It can convert Persian and Arabic digits to English digits, or English and Arabic digits to Persian digits. Non-digit text is left unchanged.

```php
Persian::number('۱۲۳٤٥۶')->toEnglish();
// 123456

Persian::number('123456')->toPersian();
// ۱۲۳۴۵۶
```

### Clean / storage normalization

Clean normalization is an alias for storage normalization. It first normalizes text, then converts digits to English for storage.

```php
Persian::clean('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷');
// علی کاظمی شماره 09121234567
```

### Display normalization

Display normalization first normalizes text, then converts digits to Persian for display.

```php
Persian::normalize('علي كاظمي شماره 09121234567')->forDisplay();
// علی کاظمی شماره ۰۹۱۲۱۲۳۴۵۶۷
```

## Not Included In Core

This core package intentionally does not include payments, SMS, PDF generation, Filament integrations, Jalali calendar support, address or city databases, mobile validation, money formatting, validation rules, or business-specific features.

## Roadmap

- Mobile normalizer
- Money formatter
- Validation rules
- Persian search package
- Filament package

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
