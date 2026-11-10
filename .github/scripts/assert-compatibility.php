<?php

declare(strict_types=1);

use Composer\InstalledVersions;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$expectedLaravelMajor = (int) getenv('EXPECTED_LARAVEL_MAJOR');
$expectedTestbenchConstraint = (string) getenv('EXPECTED_TESTBENCH_MAJOR');

if (preg_match('/^\^?(\d+)/', $expectedTestbenchConstraint, $matches) !== 1) {
    fwrite(STDERR, "Invalid expected Testbench constraint: {$expectedTestbenchConstraint}\n");

    exit(1);
}

$expectedTestbenchMajor = (int) $matches[1];
$packages = [
    'laravel/framework' => $expectedLaravelMajor,
    'orchestra/testbench' => $expectedTestbenchMajor,
];
$versions = [];

foreach ($packages as $package => $expectedMajor) {
    if (! InstalledVersions::isInstalled($package)) {
        fwrite(STDERR, "Required package {$package} is not installed.\n");

        exit(1);
    }

    $version = InstalledVersions::getPrettyVersion($package);
    $major = (int) explode('.', ltrim((string) $version, 'v'))[0];
    $versions[$package] = $version;

    if ($major !== $expectedMajor) {
        fwrite(STDERR, "Expected {$package} major {$expectedMajor}, found {$version}.\n");

        exit(1);
    }
}

if (! InstalledVersions::isInstalled('phpunit/phpunit')) {
    fwrite(STDERR, "Required package phpunit/phpunit is not installed.\n");

    exit(1);
}

$versions['phpunit/phpunit'] = InstalledVersions::getPrettyVersion('phpunit/phpunit');
$versions['PHP'] = PHP_VERSION;

foreach ($versions as $package => $version) {
    printf("%s: %s\n", $package, $version);
}
