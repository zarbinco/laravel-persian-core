<?php

namespace Zarbinco\PersianCore\Formatters;

use Zarbinco\PersianCore\Contracts\MoneyFormatterContract;
use Zarbinco\PersianCore\Contracts\MoneyNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;

class MoneyFormatter implements MoneyFormatterContract
{
    private readonly string $defaultCurrency;

    private readonly string $displayDigits;

    private readonly string $numberDisplayDigits;

    private readonly string $thousandsSeparator;

    private readonly int $rialToTomanRate;

    /** @var array<string, array<string, string>> */
    private readonly array $labels;

    /**
     * @param  array<string, mixed>  $moneyOptions
     * @param  array<string, mixed>  $numberOptions
     */
    public function __construct(
        private readonly MoneyNormalizerContract $normalizer,
        private readonly PersianNumberNormalizerContract $numberNormalizer,
        array $moneyOptions = [],
        array $numberOptions = [],
    ) {
        $this->defaultCurrency = $this->normalizeCurrency($moneyOptions['default_currency'] ?? null, 'toman');
        $this->numberDisplayDigits = $this->normalizeDigits($numberOptions['display_digits'] ?? null, 'fa');
        $this->displayDigits = $this->normalizeDigits($moneyOptions['display_digits'] ?? null, $this->numberDisplayDigits);
        $this->thousandsSeparator = $this->stringOption($moneyOptions['thousands_separator'] ?? null, ',');
        $this->rialToTomanRate = $this->rateOption($moneyOptions['rial_to_toman_rate'] ?? null);
        $this->labels = $this->labelsOption($moneyOptions['labels'] ?? null);
    }

    public function format(string|int|float|null $amount, ?string $currency = null, ?string $digits = null): string
    {
        $money = $this->normalizer->clean($amount);

        if ($money === '') {
            return '';
        }

        $targetCurrency = $this->normalizeCurrency($currency, $this->defaultCurrency);
        $targetDigits = $this->normalizeDigits($digits, $this->displayDigits);
        $formatted = $this->formatInteger($money);
        $label = $this->label($targetCurrency, $targetDigits);

        if ($targetDigits === 'fa') {
            $formatted = $this->numberNormalizer->toPersian($formatted);
        }

        return $formatted.' '.$label;
    }

    public function toman(string|int|float|null $amount, ?string $digits = null): string
    {
        return $this->format($amount, 'toman', $digits);
    }

    public function rial(string|int|float|null $amount, ?string $digits = null): string
    {
        return $this->format($amount, 'rial', $digits);
    }

    public function convertRialToToman(?int $rial): ?int
    {
        return $rial === null ? null : intdiv($rial, $this->rialToTomanRate);
    }

    public function convertTomanToRial(?int $toman): ?int
    {
        return $toman === null ? null : $toman * $this->rialToTomanRate;
    }

    public function defaultCurrency(): string
    {
        return $this->defaultCurrency;
    }

    private function formatInteger(string $money): string
    {
        $isNegative = str_starts_with($money, '-');
        $integer = $isNegative ? substr($money, 1) : $money;
        $groups = [];

        while (strlen($integer) > 3) {
            array_unshift($groups, substr($integer, -3));
            $integer = substr($integer, 0, -3);
        }

        array_unshift($groups, $integer);

        return ($isNegative ? '-' : '').implode($this->thousandsSeparator, $groups);
    }

    private function normalizeCurrency(mixed $currency, string $fallback): string
    {
        return is_string($currency) && in_array($currency, ['toman', 'rial'], true) ? $currency : $fallback;
    }

    private function normalizeDigits(mixed $digits, string $fallback): string
    {
        return is_string($digits) && in_array($digits, ['fa', 'en'], true) ? $digits : $fallback;
    }

    private function label(string $currency, string $digits): string
    {
        return $this->labels[$digits][$currency] ?? $this->fallbackLabels()[$digits][$currency];
    }

    private function stringOption(mixed $value, string $fallback): string
    {
        return is_string($value) && $value !== '' ? $value : $fallback;
    }

    private function rateOption(mixed $value): int
    {
        return is_int($value) && $value > 0 ? $value : 10;
    }

    /** @return array<string, array<string, string>> */
    private function labelsOption(mixed $labels): array
    {
        $fallback = $this->fallbackLabels();

        if (! is_array($labels)) {
            return $fallback;
        }

        foreach (['fa', 'en'] as $digits) {
            if (! is_array($labels[$digits] ?? null)) {
                $labels[$digits] = [];
            }

            foreach (['toman', 'rial'] as $currency) {
                if (! is_string($labels[$digits][$currency] ?? null) || $labels[$digits][$currency] === '') {
                    $labels[$digits][$currency] = $fallback[$digits][$currency];
                }
            }
        }

        return [
            'fa' => [
                'toman' => $labels['fa']['toman'],
                'rial' => $labels['fa']['rial'],
            ],
            'en' => [
                'toman' => $labels['en']['toman'],
                'rial' => $labels['en']['rial'],
            ],
        ];
    }

    /** @return array<string, array<string, string>> */
    private function fallbackLabels(): array
    {
        return [
            'fa' => [
                'toman' => 'تومان',
                'rial' => 'ریال',
            ],
            'en' => [
                'toman' => 'toman',
                'rial' => 'rial',
            ],
        ];
    }
}
