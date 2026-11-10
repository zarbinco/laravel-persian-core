# Verifying a release

## Compatibility

| Laravel | PHP | Testbench |
| --- | --- | --- |
| 12 | 8.2, 8.3, 8.4, 8.5 | 10 |
| 13 | 8.3, 8.4, 8.5 | 11 |

The [test workflow](../.github/workflows/tests.yml) runs tests and static analysis with the highest compatible dependencies for each combination. It also checks the lowest dependencies and installs the package in a fresh application on the minimum PHP version for each Laravel major. Pint runs in a separate job with a pinned version.

## Local checks

Run these commands from the package root with development dependencies installed.

```bash
composer validate --strict
composer diagnose
composer test
composer analyse
composer format -- --test
composer lint
composer audit
```

Use the same PHP and Testbench versions as the workflow when reproducing a matrix job locally. Keep the generated `composer.lock` out of the package repository.

Run the consumer checks from the package root in a Bash environment with PHP and Composer on `PATH`. Use PHP 8.2 or later for Laravel 12 and PHP 8.3 or later for Laravel 13.

```bash
bash .github/scripts/consumer-smoke.sh 12
bash .github/scripts/consumer-smoke.sh 13
```

The script installs the local checkout through a Composer path repository in a disposable Laravel app. It checks package discovery, provider and facade resolution, contract bindings, validation, publishing, Artisan commands, and configuration caching without using a published version of the package.

## Before tagging

Review the changelog, [upgrade guide](../UPGRADE.md), and [security policy](../SECURITY.md). Verify that GitHub accepts private vulnerability reports and that all workflow jobs pass on the exact commit you intend to tag.

```bash
git archive --format=zip --output=persian-core-dist.zip HEAD
```

Inspect the archive for `composer.json`, `src/`, `config/`, `resources/lang/`, the license, and the consumer documentation. Check that the archive excludes the test suite, CI, and maintainer-only documents. `HEAD` refers to the committed tree. Repeat this check after any release commit changes.

After tagging and publishing, confirm that Packagist lists the version and that a fresh application can install the stable tag.
