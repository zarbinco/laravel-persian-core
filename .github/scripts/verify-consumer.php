<?php

declare(strict_types=1);

use Composer\InstalledVersions;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Zarbinco\PersianCore\Contracts\IranianBankDetectorContract;
use Zarbinco\PersianCore\Contracts\MobileFormatterContract;
use Zarbinco\PersianCore\Contracts\MobileNormalizerContract;
use Zarbinco\PersianCore\Contracts\MoneyFormatterContract;
use Zarbinco\PersianCore\Contracts\MoneyNormalizerContract;
use Zarbinco\PersianCore\Contracts\NumberFormatterContract;
use Zarbinco\PersianCore\Contracts\PersianNormalizerPipelineContract;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianSearchNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;
use Zarbinco\PersianCore\PersianCoreServiceProvider;
use Zarbinco\PersianCore\PersianManager;
use Zarbinco\PersianCore\Rules\PersianMoneyAmount;

$appPath = realpath($argv[1] ?? '');
$packagePath = realpath((string) getenv('PACKAGE_ROOT'));
$expectedLaravelMajor = (int) getenv('EXPECTED_LARAVEL_MAJOR');

if ($appPath === false || $packagePath === false) {
    throw new RuntimeException('Consumer or local package path does not exist.');
}

chdir($appPath);

require $appPath.'/vendor/autoload.php';

$app = require $appPath.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$frameworkVersion = $app->version();
$frameworkMajor = (int) explode('.', $frameworkVersion)[0];

if ($frameworkMajor !== $expectedLaravelMajor) {
    throw new RuntimeException("Expected Laravel {$expectedLaravelMajor}, found {$frameworkVersion}.");
}

$packageVersion = InstalledVersions::getPrettyVersion('zarbinco/laravel-persian-core');
$packageInstallPath = InstalledVersions::getInstallPath('zarbinco/laravel-persian-core');

if ($packageVersion !== '1.0.0' || $packageInstallPath === null) {
    throw new RuntimeException("Unexpected local package installation: {$packageVersion}.");
}

$packageComposer = json_decode(
    file_get_contents($appPath.'/composer.json'),
    true,
    flags: JSON_THROW_ON_ERROR,
);

if (! isset($packageComposer['require']['zarbinco/laravel-persian-core'])) {
    throw new RuntimeException('The package is not a normal application dependency.');
}

if (isset($packageComposer['require-dev']['zarbinco/laravel-persian-core'])) {
    throw new RuntimeException('The package was incorrectly installed as a development dependency.');
}

$sourceNormalizer = $packagePath.'/src/Normalizers/MoneyNormalizer.php';
$installedNormalizer = $packageInstallPath.'/src/Normalizers/MoneyNormalizer.php';

if (! is_file($installedNormalizer) || hash_file('sha256', $sourceNormalizer) !== hash_file('sha256', $installedNormalizer)) {
    throw new RuntimeException('Installed package source does not match the current local repository.');
}

if (! isset($app->getLoadedProviders()[PersianCoreServiceProvider::class])) {
    throw new RuntimeException('Laravel package discovery did not load PersianCoreServiceProvider.');
}

$contracts = [
    PersianTextNormalizerContract::class,
    PersianNumberNormalizerContract::class,
    PersianSearchNormalizerContract::class,
    PersianNormalizerPipelineContract::class,
    NumberFormatterContract::class,
    MobileNormalizerContract::class,
    MobileFormatterContract::class,
    MoneyNormalizerContract::class,
    MoneyFormatterContract::class,
    IranianBankDetectorContract::class,
];

foreach ($contracts as $contract) {
    if (! $app->make($contract) instanceof $contract) {
        throw new RuntimeException("Container binding did not resolve {$contract}.");
    }
}

if (! $app->make(PersianManager::class) instanceof PersianManager || ! $app->make('persian-core') instanceof PersianManager) {
    throw new RuntimeException('PersianManager did not resolve from the application container.');
}

if (! class_exists('Persian') || \Persian::money('۱٬۲۵۰٬۰۰۰ ریال')->value() !== 1250000) {
    throw new RuntimeException('The discovered Persian facade did not return the expected money value.');
}

if (config('persian-core.money.default_currency') !== 'toman') {
    throw new RuntimeException('Default package configuration was not merged.');
}

$validMoney = Validator::make(
    ['amount' => '۱٬۲۵۰٬۰۰۰ ریال'],
    ['amount' => [new PersianMoneyAmount]],
);
$invalidMoney = Validator::make(
    ['amount' => '.5'],
    ['amount' => [new PersianMoneyAmount]],
);
$relaxedMoney = Validator::make(
    ['amount' => 'Price: 1500 toman'],
    ['amount' => [new PersianMoneyAmount(strict: false)]],
);
$strictMoney = Validator::make(
    ['amount' => 'Price: 1500 toman'],
    ['amount' => [new PersianMoneyAmount(strict: true)]],
);

if (! $validMoney->passes() || $invalidMoney->passes() || ! $relaxedMoney->passes() || $strictMoney->passes()) {
    throw new RuntimeException('Money validation behavior differs from its documented strict/relaxed contract.');
}

$optionalEmpty = Validator::make(['amount' => null], ['amount' => [new PersianMoneyAmount]]);
$requiredEmpty = Validator::make(
    ['amount' => null],
    ['amount' => ['required', new PersianMoneyAmount]],
);

if (! $optionalEmpty->passes() || $requiredEmpty->passes()) {
    throw new RuntimeException('Optional empty values and required validation do not compose correctly.');
}

$englishMessage = Lang::get('persian-core::validation.persian_money_amount', ['attribute' => 'amount'], 'en');
$persianMessage = Lang::get('persian-core::validation.persian_money_amount', ['attribute' => 'amount'], 'fa');

if (! is_string($englishMessage) || ! str_contains($englishMessage, 'amount')
    || ! is_string($persianMessage) || ! str_contains($persianMessage, 'مبلغ')) {
    throw new RuntimeException('English and Persian package translations did not resolve.');
}

$app->setLocale('zz');
$fallbackMessage = Lang::get('persian-core::validation.persian_money_amount', ['attribute' => 'amount']);

if ($fallbackMessage !== $englishMessage) {
    throw new RuntimeException('Application translation fallback did not resolve the English package message.');
}

foreach (['persian-core:install', 'persian-core:doctor', 'persian-core:about'] as $command) {
    if (! array_key_exists($command, Artisan::all())) {
        throw new RuntimeException("Artisan command {$command} was not registered.");
    }
}

$request = Illuminate\Http\Request::create('/');
$httpKernel = $app->make(HttpKernel::class);
$response = $httpKernel->handle($request);
$httpKernel->terminate($request, $response);

if ($response->getStatusCode() !== 200) {
    throw new RuntimeException("HTTP application boot returned status {$response->getStatusCode()}.");
}

$app['config']->set('persian-core.money.default_currency', 'invalid');
$doctorStatus = Artisan::call('persian-core:doctor');

if ($doctorStatus !== 1) {
    throw new RuntimeException('Doctor command did not reject deliberately invalid package configuration.');
}

printf(
    "Consumer verified: Laravel %s; PHP %s; package %s from %s; no-dev install; HTTP/console boot, provider, facade, contracts, rules and translations passed.\n",
    $frameworkVersion,
    PHP_VERSION,
    $packageVersion,
    $packagePath,
);
