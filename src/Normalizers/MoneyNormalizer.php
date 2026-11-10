<?php

namespace Zarbinco\PersianCore\Normalizers;

use Zarbinco\PersianCore\Contracts\MoneyNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;

class MoneyNormalizer implements MoneyNormalizerContract
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
        private readonly PersianNumberNormalizerContract $numberNormalizer,
    ) {}

    public function clean(string|int|float|null $value): string
    {
        return $this->parse($value)['amount'] ?? '';
    }

    /**
     * @return array{amount: string, currency: string|null}|null
     */
    private function parse(string|int|float|null $value): ?array
    {
        $money = $this->numberNormalizer->toEnglish($value);

        if ($money === '') {
            return null;
        }

        $money = str_replace(['−', '٫'], ['-', '.'], $money);
        if (preg_match('/(?<=[0-9])[eE]/u', $money) === 1) {
            return null;
        }

        $pattern = '/(?<digits>\p{N}+)|(?<sign>[+\-])|(?<separator>[.,٬_])|(?<space>\h+)|(?<newline>\R+)|(?<word>[\p{L}\p{M}]+)|(?<other>.)/us';

        if (preg_match_all($pattern, $money, $tokens, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL) === false) {
            return null;
        }

        $candidates = [];
        $currencies = [];
        $candidate = '';
        $hasDigits = false;

        foreach ($tokens as $index => $token) {
            if ($token['digits'] !== null) {
                if (preg_match('/^[0-9]+$/D', $token['digits']) !== 1) {
                    return null;
                }

                $candidate .= $token['digits'];
                $hasDigits = true;

                continue;
            }

            if ($token['word'] !== null) {
                $currency = $this->currencyForLabel($token['word']);

                if ($currency !== null) {
                    $currencies[] = $currency;
                }

                $this->finishCandidate($candidate, $hasDigits, $candidates);
                $candidate = '';
                $hasDigits = false;

                continue;
            }

            if ($token['sign'] !== null) {
                $candidate .= $token['sign'];

                continue;
            }

            if ($token['separator'] !== null) {
                if ($candidate !== '' || $this->separatorStartsNumericCandidate($tokens, $index)) {
                    $candidate .= $token['separator'];
                }

                continue;
            }

            if ($token['space'] !== null) {
                if ($candidate !== '' && $this->spaceJoinsNumericMaterial($tokens, $index)) {
                    $candidate .= ' ';
                } else {
                    $this->finishCandidate($candidate, $hasDigits, $candidates);
                    $candidate = '';
                    $hasDigits = false;
                }

                continue;
            }

            $this->finishCandidate($candidate, $hasDigits, $candidates);
            $candidate = '';
            $hasDigits = false;
        }

        $this->finishCandidate($candidate, $hasDigits, $candidates);

        if (count($candidates) !== 1 || $candidates[0] === '' || count($currencies) > 1) {
            return null;
        }

        return [
            'amount' => $candidates[0],
            'currency' => $currencies[0] ?? null,
        ];
    }

    /**
     * @param  list<array<int|string, string|null>>  $tokens
     */
    private function separatorStartsNumericCandidate(array $tokens, int $index): bool
    {
        return isset($tokens[$index + 1]) && $tokens[$index + 1]['digits'] !== null;
    }

    /**
     * @param  list<array<int|string, string|null>>  $tokens
     */
    private function spaceJoinsNumericMaterial(array $tokens, int $index): bool
    {
        if (! isset($tokens[$index + 1])) {
            return false;
        }

        $next = $tokens[$index + 1];

        return $next['digits'] !== null || $next['sign'] !== null || $next['separator'] !== null;
    }

    /**
     * @param  array<int, string>  $candidates
     */
    private function finishCandidate(string $candidate, bool $hasDigits, array &$candidates): void
    {
        if (! $hasDigits) {
            return;
        }

        $normalized = $this->normalizeMoneyCandidate($candidate);

        if ($normalized === null) {
            $candidates[] = '';

            return;
        }

        $candidates[] = $normalized;
    }

    private function normalizeMoneyCandidate(string $candidate): ?string
    {
        $sign = '';

        if (str_starts_with($candidate, '-') || str_starts_with($candidate, '+')) {
            $sign = $candidate[0];
            $candidate = substr($candidate, 1);
            $candidate = ltrim($candidate, ' ');
        }

        if ($candidate === '' || strpbrk($candidate, '+-') !== false || substr_count($candidate, '.') > 1) {
            return null;
        }

        $decimalPosition = strpos($candidate, '.');
        $integer = $decimalPosition === false ? $candidate : substr($candidate, 0, $decimalPosition);
        $fraction = $decimalPosition === false ? null : substr($candidate, $decimalPosition + 1);

        if ($integer === '' || ($fraction !== null && ($fraction === '' || preg_match('/^[0-9]+$/D', $fraction) !== 1))) {
            return null;
        }

        $groups = preg_split('/[,٬_ ]/u', $integer);

        if ($groups === false) {
            return null;
        }

        if (count($groups) === 1) {
            if (preg_match('/^[0-9]+$/D', $groups[0]) !== 1) {
                return null;
            }
        } else {
            if (preg_match('/^[0-9]{1,3}$/D', $groups[0]) !== 1) {
                return null;
            }

            $separators = [];

            preg_match_all('/[,٬_ ]/u', $integer, $separatorMatches);
            $separators = array_values(array_unique($separatorMatches[0]));

            if (count($separators) !== 1) {
                return null;
            }

            foreach (array_slice($groups, 1) as $group) {
                if (preg_match('/^[0-9]{3}$/D', $group) !== 1) {
                    return null;
                }
            }
        }

        if ($fraction !== null && preg_match('/[1-9]/', $fraction) === 1) {
            return null;
        }

        $digits = implode('', $groups);
        $normalized = ltrim($digits, '0');
        $normalized = $normalized === '' ? '0' : $normalized;

        return $normalized === '0' ? '0' : ($sign === '-' ? '-'.$normalized : $normalized);
    }

    private function currencyForLabel(string $label): ?string
    {
        return self::CURRENCY_LABELS[$label] ?? self::CURRENCY_LABELS[strtolower($label)] ?? null;
    }

    public function value(string|int|float|null $value): ?int
    {
        $money = $this->parse($value)['amount'] ?? '';

        if ($money === '') {
            return null;
        }

        $isNegative = str_starts_with($money, '-');
        $digits = $isNegative ? substr($money, 1) : $money;

        if ($digits === '' || preg_match('/^\d+$/u', $digits) !== 1) {
            return null;
        }

        if ($digits === '0') {
            return 0;
        }

        $limit = $isNegative ? ltrim((string) PHP_INT_MIN, '-') : (string) PHP_INT_MAX;

        if (strlen($digits) > strlen($limit) || (strlen($digits) === strlen($limit) && $digits > $limit)) {
            return null;
        }

        if ($money === (string) PHP_INT_MIN) {
            return PHP_INT_MIN;
        }

        return (int) $money;
    }

    public function detectedCurrency(string|int|float|null $value): ?string
    {
        return $this->parse($value)['currency'] ?? null;
    }
}
