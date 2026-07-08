# Laravel Persian Core

[English README](README.md)

`zarbinco/laravel-persian-core` یک پکیج سبک برای لاراول است که ابزارهای پایه برای کار با ورودی‌های فارسی و ایرانی فراهم می‌کند؛ از جمله نرمال‌سازی متن و عدد، موبایل، مبلغ، ruleهای اعتبارسنجی، و تشخیص آفلاین بانک از روی شماره کارت یا شبا.

## امکانات اصلی

- نرمال‌سازی حروف فارسی/عربی مثل `ي` و `ك`.
- تبدیل ارقام فارسی، عربی و انگلیسی.
- نرمال‌سازی برای ذخیره‌سازی، نمایش و جستجو.
- پاک‌سازی، تبدیل و فرمت عددها.
- نرمال‌سازی و فرمت شماره موبایل ایران.
- ابزارهای مبلغ برای تومان و ریال.
- ruleهای اعتبارسنجی لاراول برای ورودی‌های رایج ایرانی/فارسی.
- تشخیص آفلاین و best-effort بانک از روی BIN/IIN کارت و کد بانک در شبا.
- فایل config و فایل‌های ترجمه قابل انتشار.
- دستورهای Artisan برای نصب، بررسی وضعیت و نمایش اطلاعات پکیج.

## نصب

```bash
composer require zarbinco/laravel-persian-core
```

لاراول service provider و facade را از طریق package discovery ثبت می‌کند.

## انتشار config و lang

انتشار فایل config:

```bash
php artisan vendor:publish --tag=persian-core-config
```

انتشار ترجمه‌های validation:

```bash
php artisan vendor:publish --tag=persian-core-lang
```

یا انتشار هر دو با دستور نصب پکیج:

```bash
php artisan persian-core:install
```

برای بازنویسی فایل‌های قبلی، از گزینه `--force` استفاده کنید.

## چند نمونه استفاده

```php
use Zarbinco\PersianCore\Facades\Persian;

Persian::text('علي كاظمي')->normalize();
// علی کاظمی

Persian::number('۱۲۳٤٥۶')->toEnglish();
// 123456

Persian::clean('علي كاظمي شماره ۰۹۱۲۱۲۳۴۵۶۷');
// علی کاظمی شماره 09121234567

Persian::mobile('۰۹۱۲ ۱۲۳ ۴۵۶۷')->international();
// +989121234567

Persian::money(1250000)->format();
// ۱,۲۵۰,۰۰۰ تومان
```

تشخیص بانک:

```php
Persian::card('6037991234567890')->bankSlug();
// melli

Persian::sheba('IR170170000000000000000000')->bankNameFa();
// بانک ملی ایران
```

اعتبارسنجی در لاراول:

```php
use Zarbinco\PersianCore\Rules\IranianMobile;
use Zarbinco\PersianCore\Rules\PersianAlpha;

$request->validate([
    'name' => ['required', new PersianAlpha()],
    'mobile' => ['required', new IranianMobile()],
]);
```

## تفاوت normalization و validation

Normalizerها برای پاک‌سازی، تبدیل، فرمت و آماده‌سازی داده برای ذخیره‌سازی، نمایش یا جستجو استفاده می‌شوند. بعضی normalizerها عمدا کمی permissive هستند تا بتوانند ورودی‌های نامرتب کاربر را تمیز کنند.

Validatorها برای رد یا قبول ورودی در اعتبارسنجی لاراول هستند. آن‌ها شکل، ساختار و در موارد لازم checksum را بررسی می‌کنند، اما مالکیت واقعی، وجود حساب، وضعیت کارت یا وضعیت زنده بانکی را بررسی نمی‌کنند.

اگر خروجی تمیز و یکنواخت می‌خواهید از normalizer استفاده کنید. اگر می‌خواهید ورودی نامعتبر رد شود از ruleهای validation استفاده کنید و برای فیلدهای اجباری، rule لاراول `required` را هم اضافه کنید.

## محدودیت تشخیص بانک/کارت/شبا

تشخیص بانک در این پکیج آفلاین و best-effort است. این تشخیص فقط بر اساس داده‌های محلی BIN/IIN کارت و کد بانک در شبا انجام می‌شود و برای موارد ناشناخته مقدار `null` برمی‌گرداند.

این قابلیت ثابت نمی‌کند:

- حساب وجود دارد.
- کارت فعال است.
- کارت یا حساب متعلق به یک شخص مشخص است.
- کارت به حساب قابل تبدیل است.
- وضعیت واقعی یا آنلاین بانک تایید شده است.

برای اعتبارسنجی شماره کارت و شبا از ruleهای `IranianCardNumber` و `IranianSheba` استفاده کنید. حتی این ruleها هم فقط شکل و checksum را در محدوده پکیج بررسی می‌کنند.

## چیزهایی که این پکیج نیست

این پکیج موارد زیر را پوشش نمی‌دهد:

- درگاه پرداخت یا PSP.
- سیستم احراز مالکیت یا استعلام بانکی.
- ارسال SMS.
- تقویم جلالی.
- تولید PDF یا فاکتور.
- پنل ادمین.
- سیستم حسابداری یا مالیاتی.
- موتور جستجوی full-text یا ranking.
- دیتابیس شهر، استان یا آدرس.

## توسعه و جایگزینی سرویس‌ها

سرویس‌های اصلی پکیج به contractهای کوچک در container لاراول bind شده‌اند. اگر نیاز دارید پیاده‌سازی داخلی را جایگزین کنید، می‌توانید در service provider خودتان contract مربوط را bind کنید:

```php
use App\Support\CustomTextNormalizer;
use Zarbinco\PersianCore\Contracts\PersianTextNormalizerContract;

$this->app->bind(PersianTextNormalizerContract::class, CustomTextNormalizer::class);
```

پیاده‌سازی سفارشی باید رفتار مستندشده و نوع خروجی contract را حفظ کند. تشخیص بانک همچنان بر اساس داده آفلاین است، مگر این‌که خودتان آن سرویس را جایگزین کنید.

## سازگاری

- PHP `^8.2`.
- Laravel components `^11.0`، `^12.0` یا `^13.0`.
- License: MIT.

## مستندات انگلیسی

برای توضیحات کامل‌تر، نمونه‌های بیشتر و جزئیات config، [README انگلیسی](README.md) را ببینید.
