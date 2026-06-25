# Laravel Persian Core

`zarbinco/laravel-persian-core` is a small Laravel package for Persian text and number normalization.

The core package focuses on Persian text cleanup, digit conversion, combined storage/display/search normalization, a fluent API, and a Laravel facade.

## Installation

```bash
composer require zarbinco/laravel-persian-core
```

## Publish Config

```bash
php artisan vendor:publish --tag=persian-core-config
```

## Configuration

The default normalization configuration includes:

```php
'text' => [
    'display' => [
        'normalize_ellipsis' => true,
        'normalize_punctuation_spacing' => true,
    ],

    'search' => [
        'zwnj' => 'space',
        'remove_punctuation' => true,
        'normalize_arabic_alef' => true,
        'normalize_teh_marbuta' => true,
    ],
],

'numbers' => [
    'storage_digits' => 'en',
    'display_digits' => 'fa',
],
```

`text.display` controls display-friendly cleanup such as ellipsis normalization and punctuation spacing.

`text.search.zwnj` controls how ZWNJ is handled for search normalization. Supported values are `preserve`, `remove`, and `space`. Invalid values fall back to `space`.

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

Persian::text('سلام ،  علي !')->forDisplay();
// سلام، علی!

Persian::text('می‌روم')->forSearch();
// می روم

Persian::normalize('علي كاظمي مبلغ ۱,۲۵۰,۰۰۰ تومان')->forSearch();
// علی کاظمی مبلغ 1250000 تومان
```

## Normalization Modes

### Text normalization

Text normalization fixes Persian and Arabic text variants without changing digits. It converts Arabic Yeh and Kaf to Persian Yeh and Kaf, removes Arabic diacritics and tatweel, removes problematic invisible characters while preserving ZWNJ, and normalizes whitespace.

`normalize()` is conservative and safe. It does not apply aggressive search transformations or digit conversion.

```php
Persian::text('علي ۱۲۳')->normalize();
// علی ۱۲۳

Persian::text('علي ۱۲۳')->forStorage();
// علی ۱۲۳

Persian::text('سلام ،  علی  ! خوبی ؟')->forDisplay();
// سلام، علی! خوبی؟

Persian::text('می‌روم')->forSearch();
// می روم
```

`forStorage()` is currently the same as `normalize()` for text-only normalization.

`forDisplay()` is display-friendly and can normalize ellipsis and punctuation spacing without converting digits.

`forSearch()` is more aggressive. It can normalize ZWNJ, remove punctuation, normalize Arabic Alef variants, and normalize Teh Marbuta. It still does not convert digits directly; digit conversion stays in number normalization or the combined pipeline.

### Number normalization

Number normalization only changes digits. It can convert Persian and Arabic digits to English digits, or English and Arabic digits to Persian digits. Non-digit text is left unchanged.

```php
Persian::number('۱۲۳٤٥۶')->toEnglish();
// 123456

Persian::number('123456')->toPersian();
// ۱۲۳۴۵۶
```

### Clean / storage normalization

Clean normalization is an alias for storage normalization. It first normalizes text, then converts digits according to `numbers.storage_digits`. By default, storage digits are English.

```php
Persian::clean('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷');
// علی کاظمی شماره 09121234567
```

### Display normalization

Display normalization first applies display-friendly text normalization, then converts digits according to `numbers.display_digits`. By default, display digits are Persian.

```php
Persian::normalize('علي كاظمي شماره 09121234567')->forDisplay();
// علی کاظمی شماره ۰۹۱۲۱۲۳۴۵۶۷
```

### Search normalization

Search normalization first applies more aggressive text normalization, then converts digits to English and removes numeric separators inside number groups.

```php
Persian::normalize('علي كاظمي مبلغ ۱,۲۵۰,۰۰۰ تومان')->forSearch();
// علی کاظمی مبلغ 1250000 تومان

Persian::searchable('علي ۱۲۳');
// علی 123
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
