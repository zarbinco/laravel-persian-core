# Upgrade Guide

## Upgrading To 1.0.0

`1.0.0` is the first stable release of `zarbinco/laravel-persian-core`.

### Requirements

- PHP `^8.2`.
- Laravel components `^11.0`, `^12.0`, or `^13.0`.

### Configuration

Publish or compare the latest config:

```bash
php artisan vendor:publish --tag=persian-core-config
```

Important defaults:

```php
'numbers' => [
    'storage_digits' => 'en',
    'display_digits' => 'fa',
],
```

`Persian::clean()` and `forStorage()` use `numbers.storage_digits`. `forDisplay()` uses `numbers.display_digits`.

### Validation

Validation rules treat empty values as passing by default. Use Laravel's `required` rule for required fields.

### Removed Or Deprecated APIs

No stable APIs have been removed because this is the first stable release.
