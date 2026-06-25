# Laravel Persian Core

`zarbinco/laravel-persian-core` is a lightweight Laravel foundation package for Persian text, numbers, mobile, money, and validation utilities.

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
    'thousands_separator' => ',',
    'decimal_separator' => '.',
],

'mobile' => [
    'default_country' => 'IR',
    'iran' => [
        'country_code' => '98',
        'national_prefix' => '0',
        'mask_pattern' => '0912***4567',
    ],
],

'money' => [
    'default_currency' => 'toman',
    'display_digits' => 'fa',
    'thousands_separator' => ',',
    'rial_to_toman_rate' => 10,

    'labels' => [
        'fa' => [
            'toman' => 'تومان',
            'rial' => 'ریال',
        ],
        'en' => [
            'toman' => 'toman',
            'rial' => 'rial',
        ],
    ],
],

'validation' => [
    'empty_values_pass' => true,

    'iranian_mobile' => [
        'strict_operator_prefixes' => false,
    ],

    'iranian_postal_code' => [
        'reject_repeated_digits' => true,
    ],

    'iranian_card_number' => [
        'require_luhn' => true,
        'require_iranian_bin' => false,
    ],
],

'developer_experience' => [
    'str_macros' => [
        'enabled' => false,
    ],
],
```

`text.display` controls display-friendly cleanup such as ellipsis normalization and punctuation spacing.

`text.search.zwnj` controls how ZWNJ is handled for search normalization. Supported values are `preserve`, `remove`, and `space`. Invalid values fall back to `space`.

`numbers.thousands_separator` and `numbers.decimal_separator` control formatted numeric output. By default, formatted numbers use `,` for thousands and `.` for decimals.

`numbers.storage_digits` controls the output of storage normalization:

- `en` means `forStorage()` and `clean()` return English digits.
- `fa` means `forStorage()` and `clean()` return Persian digits.

`numbers.display_digits` controls the output of display normalization:

- `fa` means `forDisplay()` returns Persian digits.
- `en` means `forDisplay()` returns English digits.

`mobile.iran` controls the default Iranian mobile country code, national prefix, and mask pattern used by the mobile normalization foundation.

`money.default_currency` controls the default label used by `Persian::money(...)->format()`. Supported currencies are `toman` and `rial`.

`money.rial_to_toman_rate` controls conversion helpers. By default, 1 toman equals 10 rial.

`validation` controls lightweight validation-rule behavior. Empty values pass by default; use Laravel's `required` rule when a field must be present.

`developer_experience.str_macros.enabled` is reserved for optional Str macro support and is disabled by default.

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

Persian::number('۱,۲۵۰,۰۰۰')->clean();
// 1250000

Persian::number('۱۲۳۴۵۶۷')->format();
// ۱,۲۳۴,۵۶۷

Persian::mobile('۰۹۱۲ ۱۲۳ ۴۵۶۷')->normalize();
// 09121234567

Persian::mobile('09121234567')->international();
// +989121234567

Persian::money(1250000)->format();
// ۱,۲۵۰,۰۰۰ تومان
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

### Number normalization and parsing

Number normalization only changes digits when using `toEnglish()` or `toPersian()`. Non-digit text is left unchanged.

Number parsing methods are more focused on numeric strings:

- `toEnglish()` converts Persian and Arabic digits to English digits.
- `toPersian()` converts English and Arabic digits to Persian digits.
- `clean()` converts to an English numeric string, removes thousands separators, normalizes decimal and minus signs, and extracts the numeric value.
- `digitsOnly()` extracts only English digits.
- `format()` formats the cleaned number with thousands separators and configurable output digits.

```php
Persian::number('۱۲۳٤٥۶')->toEnglish();
// 123456

Persian::number('123456')->toPersian();
// ۱۲۳۴۵۶

Persian::number('۱,۲۵۰,۰۰۰')->clean();
// 1250000

Persian::number('abc ۱۲۳ def')->digitsOnly();
// 123

Persian::number('۱۲۳')->toInt();
// 123

Persian::number('۱۲٫۵')->toFloat();
// 12.5

Persian::number('۱۲۳۴۵۶۷')->isNumeric();
// true

Persian::number('۱۲۳۴۵۶۷')->format();
// ۱,۲۳۴,۵۶۷

Persian::number('۱۲۳۴۵۶۷')->format('en');
// 1,234,567
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

### Mobile normalization

The fluent mobile API is for normalization and formatting. Laravel validation is available separately through the `IranianMobile` rule.

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

The default mobile mask can be configured with `mobile.iran.mask_pattern`. Patterns are for 11-digit national mobile numbers and must be 11 characters long with one or more `*` characters.

```php
'mobile' => [
    'iran' => [
        'mask_pattern' => '09*******67',
    ],
],
```

### Money parsing and formatting

Money support is parsing and formatting only. Phase 4 supports `toman` and `rial`, integer conversion between them, and configurable labels and display digits.

Laravel validation for parseable money input is available separately through `PersianMoneyAmount`.

```php
Persian::money('۱,۲۵۰,۰۰۰ تومان')->value();
// 1250000

Persian::money('۱۲,۵۰۰,۰۰۰ ریال')->detectedCurrency();
// rial

Persian::money(1250000)->format();
// ۱,۲۵۰,۰۰۰ تومان

Persian::money(1250000)->toman();
// ۱,۲۵۰,۰۰۰ تومان

Persian::money(12500000)->rial();
// ۱۲,۵۰۰,۰۰۰ ریال

Persian::money(1250000)->format('toman', 'en');
// 1,250,000 toman

Persian::money(12500000)->fromRial()->toToman();
// 1250000

Persian::money(1250000)->fromToman()->toRial();
// 12500000

Persian::money(12500000)->fromRial()->formatToman();
// ۱,۲۵۰,۰۰۰ تومان

Persian::money(1250000)->fromToman()->formatRial();
// ۱۲,۵۰۰,۰۰۰ ریال
```

The default conversion rate is `1 toman = 10 rial`, configurable through `money.rial_to_toman_rate`.

### Validation rules

The package includes modern Laravel validation Rule objects. Empty strings and null values pass these rules by default, so Laravel's `required` rule should be used for requiredness.

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

- `IranianMobile` checks normalized Iranian mobile shape, not operator ownership or strict operator prefixes.
- `IranianSheba` checks format and IBAN checksum, not bank account ownership.
- `IranianCardNumber` checks shape, Luhn by default, and optionally a small Iranian BIN list, not card ownership.
- `PersianMoneyAmount` checks shape and parseability, not business min/max rules.

### Artisan commands

The package includes a few safe, idempotent Artisan commands for developer experience:

```bash
php artisan persian-core:install
php artisan persian-core:doctor
php artisan persian-core:about
```

`persian-core:install` publishes the package config and translations. Use `--force` to overwrite existing published files.

`persian-core:doctor` checks common setup and configuration mistakes, including digit mode values, search ZWNJ mode, money settings, validation toggles, translation loading, and optional `ext-intl` availability.

`persian-core:about` prints a quick package summary, PHP/Laravel versions, active core config values, available modules, and available package commands.

## Not Included In Core

This core package intentionally does not include payment gateways, SMS sending, invoice/PDF generation, tax/Modian integration, accounting ledgers, Filament integrations, Jalali calendar support, address or city databases, or business-specific workflows.

## Roadmap

- Developer experience commands
- Doctor command
- Optional string validation aliases
- Stabilization for v1.0.0
- Persian search package
- Filament package

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
