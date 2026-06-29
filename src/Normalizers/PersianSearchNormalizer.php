<?php

namespace Zarbinco\PersianCore\Normalizers;

use Zarbinco\PersianCore\Contracts\Normalizer;

class PersianSearchNormalizer implements Normalizer
{
    private const ZWNJ = "\u{200C}";

    /** @var array<string, mixed> */
    private array $options;

    /** @param array<string, mixed> $options */
    public function __construct(
        private readonly PersianNumberNormalizer $numberNormalizer,
        array $options = [],
    ) {
        $this->options = array_replace_recursive([
            'normalize_arabic_yeh' => true,
            'normalize_arabic_kaf' => true,
            'remove_diacritics' => true,
            'remove_tatweel' => true,
            'normalize_whitespace' => true,
            'remove_invisible_characters' => true,

            'search' => [
                'zwnj' => 'space',
                'remove_punctuation' => true,
                'normalize_arabic_alef' => true,
                'normalize_madda_alef' => true,
                'normalize_teh_marbuta' => true,
            ],
        ], $options);
    }

    public function normalize(string|int|float|null $value): string
    {
        $text = $this->normalizeText($this->stringValue($value));

        if ($this->enabled('search.normalize_arabic_alef')) {
            $text = strtr($text, [
                'أ' => 'ا',
                'إ' => 'ا',
                'ٱ' => 'ا',
            ]);
        }

        if ($this->enabled('search.normalize_madda_alef')) {
            $text = str_replace('آ', 'ا', $text);
        }

        if ($this->enabled('search.normalize_teh_marbuta')) {
            $text = str_replace('ة', 'ه', $text);
        }

        $text = match ($this->searchZwnjOption()) {
            'preserve' => $text,
            'remove' => str_replace(self::ZWNJ, '', $text),
            default => str_replace(self::ZWNJ, ' ', $text),
        };

        $text = $this->numberNormalizer->toEnglish($text);

        if ($this->enabled('search.remove_punctuation')) {
            $text = (string) preg_replace('/[\p{P}\p{S}]+/u', ' ', $text);
        }

        $text = (string) preg_replace('/(?<=\d)[,٬_](?=\d)/u', '', $text);
        $text = (string) preg_replace('/(?<=\d)\s+(?=\d)/u', '', $text);

        return $this->normalizeWhitespace($text);
    }

    /** @return array<int, string> */
    public function tokens(string|int|float|null $value): array
    {
        $normalized = $this->normalize($value);

        if ($normalized === '') {
            return [];
        }

        $parts = preg_split('/\s+/u', $normalized);

        if ($parts === false) {
            return [];
        }

        $tokens = [];

        foreach ($parts as $part) {
            if ($part !== '') {
                $tokens[] = $part;
            }
        }

        return $tokens;
    }

    private function normalizeText(string $text): string
    {
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

        return $this->enabled('normalize_whitespace')
            ? $this->normalizeWhitespace($text)
            : trim($text);
    }

    private function enabled(string $option): bool
    {
        return (bool) $this->option($option, false);
    }

    private function option(string $key, mixed $default = null): mixed
    {
        $value = $this->options;

        foreach (explode('.', $key) as $segment) {
            if (! is_array($value) || ! array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    private function searchZwnjOption(): string
    {
        $value = $this->option('search.zwnj', 'space');

        return is_string($value) && in_array($value, ['preserve', 'remove', 'space'], true)
            ? $value
            : 'space';
    }

    private function normalizeWhitespace(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    private function stringValue(string|int|float|null $value): string
    {
        return $value === null ? '' : (string) $value;
    }
}
