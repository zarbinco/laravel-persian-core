# Upgrading to Laravel Persian Core 1.0

This guide covers the upcoming v1.0.0 release and uses v0.2.0 as the starting point.

## Laravel and PHP

v0.2.0 is the last release that supports Laravel 11. Upgrade your application to Laravel 12 or 13 before installing v1.0.0.

| Laravel | Supported PHP versions |
| --- | --- |
| 12 | 8.2, 8.3, 8.4, 8.5 |
| 13 | 8.3, 8.4, 8.5 |

After v1.0.0 appears on Packagist, update the package requirement from your application root and run your tests.

```bash
composer require "zarbinco/laravel-persian-core:^1.0"
```

If your deployment caches configuration, rebuild the cache as part of the deployment. Keep any existing overrides in `config/persian-core.php`. No keys or defaults changed from v0.2.0.

Existing facade calls, extension contracts and rule constructors still use the same signatures. Review the input and output changes below if you use the bundled implementations.

## Money inputs and validation

Money is represented as a whole number of toman or rial. An amount such as `12.0` or `۱۲٫۰۰` still parses as the integer `12`. A nonzero fraction such as `12.5` now fails validation instead of being truncated.

| Input | v1.0 behavior |
| --- | --- |
| `۱٬۲۵۰٬۰۰۰ ریال` | Parses as `1250000` with currency `rial`. |
| `12,34`, `1٬000,000`, `1e3` | Rejected as malformed amounts. |
| `100 تومان 200 ریال` | Rejected because it contains more than one amount. |
| `१२३` or `１２３` | Rejected. Use ASCII, Persian or Arabic-Indic digits. |

Thousands groups must use one separator consistently, with one to three leading digits followed by groups of three. Horizontal whitespace can act as a grouping separator, so `100 200` is interpreted as `100200`. Use separate fields for separate amounts.

`PersianMoneyAmount` still uses strict validation by default. The entire field must be an amount with an optional currency suffix. Use `new PersianMoneyAmount(strict: false)` for fields such as `Price: 1500 toman` where you expect a single amount inside surrounding text. Both modes reject malformed and out-of-range amounts. The `validation.empty_values_pass` default still allows empty optional fields. Add Laravel's `required` rule when the field is mandatory.

`Persian::money($input)->value()` and currency conversions return `null` if the result cannot fit in a PHP integer. Check that return value before saving or using an amount in calculations. `clean()` and `format()` work with strings and can display a well-formed amount outside that range.

`detectedCurrency()` now matches currency labels as whole words and returns `null` for malformed amounts or multiple currency labels. Use `toRial()` or `toToman()` to convert between units.

## Search and bank data

- Use the same version of the search normalizer for stored index values and incoming queries. Whitespace-separated numbers such as `۱۴۰۴ ۱۴۰۵` now stay separate. Compare representative queries and rebuild derived search fields from the original text if needed.
- If you enabled `validation.iranian_card_number.require_iranian_bin`, test your accepted card prefixes. The rule now uses the bundled bank lookup list. The prefixes `627961`, `505416` and `505801` no longer pass this check. Luhn validation remains enabled by default, and bank lookup remains an offline metadata lookup.
- Check a sample of imported amounts against their original inputs and confirm that saved mobile numbers still pass your application's validation. Address affected records through your normal data review process.

## Upgrading from v0.1.0

Strict validation, the dedicated search helper, offline bank lookup and extension contracts first shipped in [v0.2.0](CHANGELOG.md#020---2026-07-09). If you are skipping v0.2.0, review any rules that previously accepted an identifier embedded in free text. Test the affected fields with the default `validation.strict` setting and use a per-rule override where your application intentionally accepts those inputs.
