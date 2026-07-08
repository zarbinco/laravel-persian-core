<?php

namespace Zarbinco\PersianCore\Normalizers;

use Zarbinco\PersianCore\Contracts\MobileNormalizerContract;
use Zarbinco\PersianCore\Contracts\PersianNumberNormalizerContract;

class MobileNormalizer implements MobileNormalizerContract
{
    private readonly string $countryCode;

    private readonly string $nationalPrefix;

    /** @param array<string, mixed> $options */
    public function __construct(
        private readonly PersianNumberNormalizerContract $numberNormalizer,
        array $options = [],
    ) {
        $iran = is_array($options['iran'] ?? null) ? $options['iran'] : [];

        $this->countryCode = $this->stringOption($iran['country_code'] ?? null, '98');
        $this->nationalPrefix = $this->stringOption($iran['national_prefix'] ?? null, '0');
    }

    public function normalize(string|int|float|null $value): string
    {
        return $this->national($value);
    }

    public function national(string|int|float|null $value): string
    {
        $clean = $this->clean($value);

        if ($clean === '') {
            return '';
        }

        if (str_starts_with($clean, '+'.$this->countryCode)) {
            return $this->nationalFromSubscriber(substr($clean, strlen($this->countryCode) + 1), $clean);
        }

        $digits = $this->digits($clean);

        if (str_starts_with($digits, '00'.$this->countryCode)) {
            return $this->nationalFromSubscriber(substr($digits, strlen($this->countryCode) + 2), $digits);
        }

        if (str_starts_with($digits, $this->countryCode)) {
            return $this->nationalFromSubscriber(substr($digits, strlen($this->countryCode)), $digits);
        }

        if (preg_match('/^9\d{9}$/', $digits) === 1) {
            return $this->nationalPrefix.$digits;
        }

        if (preg_match('/^'.preg_quote($this->nationalPrefix, '/').'9\d{9}$/', $digits) === 1) {
            return $digits;
        }

        return $digits;
    }

    public function clean(string|int|float|null $value): string
    {
        $mobile = trim($this->numberNormalizer->toEnglish($value));
        $mobile = (string) preg_replace('/[\s\-\x{2010}-\x{2015}().]+/u', '', $mobile);

        return (string) preg_replace('/(?!^\+)[^\d]/u', '', $mobile);
    }

    public function digits(string|int|float|null $value): string
    {
        return (string) preg_replace('/\D+/u', '', $this->numberNormalizer->toEnglish($value));
    }

    private function nationalFromSubscriber(string $subscriber, string $fallback): string
    {
        return preg_match('/^9\d{9}$/', $subscriber) === 1
            ? $this->nationalPrefix.$subscriber
            : $this->digits($fallback);
    }

    private function stringOption(mixed $value, string $fallback): string
    {
        return is_string($value) && $value !== '' ? $value : $fallback;
    }
}
