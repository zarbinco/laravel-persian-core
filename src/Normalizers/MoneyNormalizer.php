<?php

namespace Zarbinco\PersianCore\Normalizers;

class MoneyNormalizer
{
    /** @var array<string, string> */
    private const CURRENCY_LABELS = [
        'تومان' => 'toman',
        'تومن' => 'toman',
        'ریال' => 'rial',
        'ريال' => 'rial',
        'toman' => 'toman',
        'rial' => 'rial',
    ];

    public function __construct(
        private readonly PersianNumberNormalizer $numberNormalizer,
    ) {}

    public function clean(string|int|float|null $value): string
    {
        $money = $this->numberNormalizer->toEnglish($value);

        if (trim($money) === '') {
            return '';
        }

        $money = str_replace(['−', '٫'], ['-', '.'], $money);
        $money = str_replace(['تومان', 'تومن', 'ریال', 'ريال'], '', $money);
        $money = str_ireplace(['toman', 'rial'], '', $money);
        $money = str_replace(['٬', ',', '_'], '', $money);
        $money = (string) preg_replace('/\s+/u', '', $money);

        preg_match('/-?(?:\d+(?:\.\d*)?|\.\d+)/u', $money, $matches);

        if (! isset($matches[0])) {
            return '';
        }

        $number = $matches[0];
        $isNegative = str_starts_with($number, '-');
        $unsigned = $isNegative ? substr($number, 1) : $number;
        $integer = explode('.', $unsigned, 2)[0];

        if ($integer === '') {
            return '';
        }

        return ($isNegative ? '-' : '').$integer;
    }

    public function value(string|int|float|null $value): ?int
    {
        $money = $this->clean($value);

        return $money === '' ? null : (int) $money;
    }

    public function detectedCurrency(string|int|float|null $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $money = (string) $value;
        $matches = [];

        foreach (self::CURRENCY_LABELS as $label => $currency) {
            $pattern = preg_quote($label, '/');

            if (preg_match('/'.$pattern.'/iu', $money, $match, PREG_OFFSET_CAPTURE) === 1) {
                $matches[] = [
                    'offset' => $match[0][1],
                    'currency' => $currency,
                ];
            }
        }

        if ($matches === []) {
            return null;
        }

        usort($matches, fn (array $first, array $second): int => $first['offset'] <=> $second['offset']);

        return $matches[0]['currency'];
    }
}
