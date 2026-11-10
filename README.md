# Laravel Persian Core

[![Tests](https://github.com/zarbinco/laravel-persian-core/actions/workflows/tests.yml/badge.svg)](https://github.com/zarbinco/laravel-persian-core/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/zarbinco/laravel-persian-core.svg)](https://packagist.org/packages/zarbinco/laravel-persian-core)
[![Total Downloads](https://img.shields.io/packagist/dt/zarbinco/laravel-persian-core.svg)](https://packagist.org/packages/zarbinco/laravel-persian-core)
[![License](https://img.shields.io/packagist/l/zarbinco/laravel-persian-core.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/packagist/php-v/zarbinco/laravel-persian-core.svg)](composer.json)

Laravel Persian Core normalizes Persian text, digits, and Iranian mobile numbers in Laravel applications. It also parses and formats toman and rial amounts, provides validation rules for common Iranian inputs, and looks up bank metadata from card prefixes and Sheba (Iranian IBAN) codes.

[Read this README in Persian](README.fa.md).

The package covers input handling. Payment processing, live bank verification, SMS delivery, Jalali dates, invoicing, and accounting are outside its scope.

## Compatibility

The current source supports the following Laravel and PHP combinations.

- Laravel 12: PHP 8.2, 8.3, 8.4, and 8.5.
- Laravel 13: PHP 8.3, 8.4, and 8.5.

The Composer constraints are PHP `^8.2` and Illuminate `^12.0` or `^13.0`, subject to the framework's PHP requirements. For changes since the published `v0.2.0`, see the [upgrade guide](UPGRADE.md).

## Installation

```bash
composer require zarbinco/laravel-persian-core
```

See [Packagist](https://packagist.org/packages/zarbinco/laravel-persian-core) for the requirements of published versions. Laravel package discovery registers the service provider and facade automatically.

## Publishing

The package loads its defaults and validation messages automatically. To customize them, publish the config to `config/persian-core.php` or the translations to `vendor/persian-core` under your app's language directory.

```bash
php artisan vendor:publish --tag=persian-core-config
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

Use the same search normalization for indexed text and queries. It normalizes Persian and Arabic letter variants, removes diacritics and tatweel, and converts Persian and Arabic-Indic digits to ASCII. By default it replaces punctuation and zero-width non-joiners (ZWNJ) with spaces. It joins well-formed thousands groups such as `۱۲٬۵۰۰`, while `1,000 200` becomes `1000 200` and `12 + 34` becomes `12 34`.

```php
Persian::search('كیكِ شکلاتي')->normalize();
// کیک شکلاتی

Persian::search('آب‌میوه سن‌ایچ ۱۲,۵۰۰ تومان!')->normalize();
// اب میوه سن ایچ 12500 تومان

Persian::search('آب‌میوه سن‌ایچ')->tokens();
// ['اب', 'میوه', 'سن', 'ایچ']
```

`Persian::searchable($value)` returns the same normalized string directly. For example, an application can normalize a product name before writing its search column and apply the same transformation to user queries.

```php
$product->searchable_name = Persian::searchable($product->name);
$product->save();

$query = Persian::searchable($request->string('q')->toString());

Product::query()
    ->where('searchable_name', 'like', "%{$query}%")
    ->get();
```

Your application supplies the search backend, matching rules, and ranking. Search normalization works independently of Scout and database-specific search features.

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

Configure masking with `mobile.iran.mask_pattern`. Invalid patterns fall back to `0912***4567`. The mobile helpers may return cleaned input when it cannot be formatted as an Iranian mobile number. Apply the `IranianMobile` rule before storing a number that must be valid or relying on the mask.

## Money

Money helpers work with whole toman and rial amounts. `value()` returns an `int` or `null` for invalid input or an amount outside PHP's integer range.

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

The parser accepts Persian, Arabic-Indic, and ASCII digits. It accepts zero-only fractions such as `۱۲٫۰۰` and rejects nonzero fractions such as `12.5`, malformed grouping, multiple amounts, and other Unicode digit forms. It can extract one amount from surrounding text, such as `قیمت: ۱۵۰۰ تومان`. Horizontal spaces can act as thousands separators, so `100 200` is read as `100200`. The `PersianMoneyAmount` rule in strict mode requires the entire input to match the amount format.

`format()` returns a string, or `''` if the input cannot be parsed. It can display valid integer strings beyond the PHP integer range. Passing a currency to `format()` selects the label. Use `toToman()` or `toRial()` to convert the integer value, or `formatToman()` and `formatRial()` to display a converted amount.

The default rate is 10 rial per toman, configured through `money.rial_to_toman_rate`. Rial-to-toman conversion drops the remainder toward zero. Toman-to-rial conversion returns `null` if the result would overflow.

## Bank Detection

Bank lookup uses bundled metadata for Iranian card prefixes (BIN/IIN) and Sheba bank codes. It runs offline and returns `null` when there is no match.

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

Use `IranianCardNumber` and `IranianSheba` to validate identifiers. Bank lookup alone checks the metadata match. Ownership, account existence, and live card or account status require a separate verification service.

## Validation

Use Laravel validation rules to reject bad input before normalizing it for storage or display. Empty values pass by default. Add Laravel's `required` rule when a field must be present.

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

`IranianMobile` checks Iranian mobile number structure. `IranianSheba` checks the format and IBAN checksum. `IranianCardNumber` checks the format and Luhn checksum by default. Enable `validation.iranian_card_number.require_iranian_bin` to also require a prefix in the bundled dataset. Add your application's own limits for amounts accepted by `PersianMoneyAmount`.

### Strict Validation Mode

Strict mode is enabled by default for the mobile, national code, postal code, card number, Sheba, and money rules. It checks the whole input and rejects extra text around an otherwise valid value.

```php
'validation' => [
    'strict' => true,
    'empty_values_pass' => true,
],
```

For fields that need relaxed input handling, change the setting globally or pass `strict` to the relevant rule. Relaxed rules still apply their underlying checks.

```php
use Zarbinco\PersianCore\Rules\IranianCardNumber;
use Zarbinco\PersianCore\Rules\IranianMobile;

config(['persian-core.validation.strict' => false]);

new IranianMobile(strict: false);
new IranianCardNumber(strict: true);
```

Strict validation accepts Persian, Arabic-Indic, and ASCII digits in supported formats, such as `0912-123-4567`, `6037 9900 0000 0006`, `IR18 0100 0000 0000 0000 0000 00`, and `۱,۲۵۰,۰۰۰ تومان`.

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
- `banks` and `bank_data`: informational settings for the bundled offline bank dataset.
- `validation`: validation-rule strictness and empty-value behavior.

## Extending / Overriding Services

Core services are bound to contracts so applications can supply their own implementations. Register the binding in your service provider before the facade or manager is first resolved.

```php
use App\Support\CustomTextNormalizer;
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;

$this->app->bind(PersianTextNormalizerContract::class, CustomTextNormalizer::class);
```

Custom implementations should preserve the inputs, return types, and documented behavior of the contract.

## Support and contributions

See the [support guide](SUPPORT.md) for bug reports, the [security policy](SECURITY.md) for vulnerability reports, and the [contributing guide](CONTRIBUTING.md) for local setup and quality checks.

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
