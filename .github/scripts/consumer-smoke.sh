#!/usr/bin/env bash
set -euo pipefail

laravel_major="${1:?Laravel major is required}"
package_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
app_dir="$(mktemp -d "${TMPDIR:-/tmp}/laravel-persian-core-consumer-${laravel_major}-XXXXXX")"

cleanup() {
    rm -rf "$app_dir"
}
trap cleanup EXIT

export PACKAGE_ROOT="$package_root"
export EXPECTED_LARAVEL_MAJOR="$laravel_major"
export APP_DIR="$app_dir"

composer create-project \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    "laravel/laravel:^${laravel_major}.0" \
    "$app_dir"

cd "$app_dir"

php -r '
$path = "composer.json";
$composer = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
$composer["repositories"]["persian-core-local"] = [
    "type" => "path",
    "url" => getenv("PACKAGE_ROOT"),
    "options" => [
        "symlink" => false,
        "versions" => ["zarbinco/laravel-persian-core" => "1.0.0"],
    ],
];
file_put_contents($path, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
'

composer require \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    'zarbinco/laravel-persian-core:^1.0'

composer install --no-dev --no-interaction --no-progress --prefer-dist

php "$package_root/.github/scripts/verify-consumer.php" "$app_dir"

php artisan list --raw | grep -E '^persian-core:install[[:space:]]'
php artisan list --raw | grep -E '^persian-core:doctor[[:space:]]'
php artisan list --raw | grep -E '^persian-core:about[[:space:]]'
php artisan persian-core:about
php artisan persian-core:doctor

php artisan vendor:publish --tag=persian-core-config --no-interaction
php artisan vendor:publish --tag=persian-core-lang --no-interaction
test -f config/persian-core.php
test -f lang/vendor/persian-core/en/validation.php
test -f lang/vendor/persian-core/fa/validation.php

printf "<?php return ['money' => ['default_currency' => 'rial']];\n" > config/persian-core.php
printf "<?php return ['smoke' => 'custom'];\n" > lang/vendor/persian-core/en/validation.php
php artisan vendor:publish --tag=persian-core-config --no-interaction
php artisan vendor:publish --tag=persian-core-lang --no-interaction
grep -q "'default_currency' => 'rial'" config/persian-core.php
grep -q "'smoke' => 'custom'" lang/vendor/persian-core/en/validation.php

php artisan vendor:publish --tag=persian-core-config --force --no-interaction
php artisan vendor:publish --tag=persian-core-lang --force --no-interaction
grep -q "'default_currency' => 'toman'" config/persian-core.php
grep -q 'persian_money_amount' lang/vendor/persian-core/en/validation.php

php artisan persian-core:install
php artisan persian-core:install
printf "<?php return ['money' => ['default_currency' => 'rial']];\n" > config/persian-core.php
php artisan persian-core:install
grep -q "'default_currency' => 'rial'" config/persian-core.php
php artisan persian-core:install --force
grep -q "'default_currency' => 'toman'" config/persian-core.php

php artisan config:cache
php "$package_root/.github/scripts/verify-consumer.php" "$app_dir"
php artisan config:clear
php artisan package:discover
php "$package_root/.github/scripts/verify-consumer.php" "$app_dir"

printf 'Consumer smoke passed: Laravel %s, PHP %s, package source %s\n' \
    "$(php artisan --version | sed -E 's/.* ([0-9]+\.[0-9]+\.[0-9]+)$/\1/')" \
    "$(php -r 'echo PHP_VERSION;')" \
    "$package_root"
