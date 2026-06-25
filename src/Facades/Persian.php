<?php

namespace Zarbinco\PersianCore\Facades;

use Illuminate\Support\Facades\Facade;
use Zarbinco\PersianCore\PersianManager;

/**
 * @method static \Zarbinco\PersianCore\Support\PersianString text(string|int|float|null $value)
 * @method static \Zarbinco\PersianCore\Support\PersianNumber number(string|int|float|null $value)
 * @method static \Zarbinco\PersianCore\Support\PersianMobile mobile(string|int|float|null $value)
 * @method static \Zarbinco\PersianCore\Support\PersianMoney money(string|int|float|null $value)
 * @method static \Zarbinco\PersianCore\Support\PersianNormalizedString normalize(string|int|float|null $value)
 * @method static string clean(string|int|float|null $value)
 * @method static string searchable(string|int|float|null $value)
 *
 * @see PersianManager
 */
class Persian extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'persian-core';
    }
}
