<?php

namespace Zarbinco\PersianCore\Normalizers;

use Zarbinco\PersianCore\Contracts\Normalizer;

class PersianTextNormalizer implements Normalizer
{
    /** @var array<string, bool> */
    private array $options;

    /** @param array<string, bool> $options */
    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'normalize_arabic_yeh' => true,
            'normalize_arabic_kaf' => true,
            'remove_diacritics' => true,
            'remove_tatweel' => true,
            'normalize_whitespace' => true,
            'remove_invisible_characters' => true,
        ], $options);
    }

    public function normalize(string|int|float|null $value): string
    {
        $text = $this->stringValue($value);

        if ($this->enabled('normalize_arabic_yeh')) {
            $text = strtr($text, [
                'ي' => 'ی',
                'ى' => 'ی',
            ]);
        }

        if ($this->enabled('normalize_arabic_kaf')) {
            $text = strtr($text, [
                'ك' => 'ک',
            ]);
        }

        if ($this->enabled('remove_diacritics')) {
            $text = (string) preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text);
        }

        if ($this->enabled('remove_tatweel')) {
            $text = str_replace('ـ', '', $text);
        }

        if ($this->enabled('remove_invisible_characters')) {
            $text = (string) preg_replace('/[\x{200B}\x{200D}\x{200E}\x{200F}\x{202A}-\x{202E}\x{2060}-\x{206F}\x{FEFF}]/u', '', $text);
        }

        if ($this->enabled('normalize_whitespace')) {
            $text = (string) preg_replace('/[ \t\r\n\f\v]+/u', ' ', $text);
        }

        return trim($text);
    }

    private function enabled(string $option): bool
    {
        return (bool) ($this->options[$option] ?? false);
    }

    private function stringValue(string|int|float|null $value): string
    {
        return $value === null ? '' : (string) $value;
    }
}
