<?php

namespace Zarbinco\PersianCore\Normalizers;

use Zarbinco\PersianCore\Contracts\Normalizer;

class PersianTextNormalizer implements Normalizer
{
    private const ZWNJ = "\u{200C}";

    /** @var array<int, string> */
    private const SEARCH_PUNCTUATION = [
        '،',
        '؛',
        '؟',
        '!',
        '.',
        ':',
        ',',
        ';',
        '?',
        '"',
        "'",
        '“',
        '”',
        '‘',
        '’',
        '«',
        '»',
        '(',
        ')',
        '[',
        ']',
        '{',
        '}',
    ];

    /** @var array<string, mixed> */
    private array $options;

    /** @param array<string, mixed> $options */
    public function __construct(array $options = [])
    {
        $this->options = array_replace_recursive([
            'normalize_arabic_yeh' => true,
            'normalize_arabic_kaf' => true,
            'remove_diacritics' => true,
            'remove_tatweel' => true,
            'normalize_whitespace' => true,
            'remove_invisible_characters' => true,

            'display' => [
                'normalize_ellipsis' => true,
                'normalize_punctuation_spacing' => true,
            ],

            'search' => [
                'zwnj' => 'space',
                'remove_punctuation' => true,
                'normalize_arabic_alef' => true,
                'normalize_teh_marbuta' => true,
            ],
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

    public function forStorage(string|int|float|null $value): string
    {
        return $this->normalize($value);
    }

    public function forDisplay(string|int|float|null $value): string
    {
        $text = $this->normalize($value);

        if ($this->enabled('display.normalize_ellipsis')) {
            $text = (string) preg_replace('/\.{3,}/u', '…', $text);
        }

        if ($this->enabled('display.normalize_punctuation_spacing')) {
            $text = $this->normalizePunctuationSpacing($text);
        }

        return trim($text);
    }

    public function forSearch(string|int|float|null $value): string
    {
        $text = $this->normalize($value);

        if ($this->enabled('search.normalize_arabic_alef')) {
            $text = strtr($text, [
                'أ' => 'ا',
                'إ' => 'ا',
                'ٱ' => 'ا',
            ]);
        }

        if ($this->enabled('search.normalize_teh_marbuta')) {
            $text = str_replace('ة', 'ه', $text);
        }

        $text = match ($this->searchZwnjOption()) {
            'preserve' => $text,
            'remove' => str_replace(self::ZWNJ, '', $text),
            default => str_replace(self::ZWNJ, ' ', $text),
        };

        if ($this->enabled('search.remove_punctuation')) {
            $text = str_replace(self::SEARCH_PUNCTUATION, ' ', $text);
        }

        return $this->normalizeWhitespace($text);
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

    private function normalizePunctuationSpacing(string $text): string
    {
        $protectedTokens = [];
        $text = $this->protectDisplayTokens($text, $protectedTokens);

        $text = (string) preg_replace('/\s+([،؛؟!\.:])/u', '$1', $text);
        $text = (string) preg_replace('/([،؛؟!\.:])\s*(?=\S)/u', '$1 ', $text);

        return strtr($this->normalizeWhitespace($text), $protectedTokens);
    }

    /** @param array<string, string> $protectedTokens */
    private function protectDisplayTokens(string $text, array &$protectedTokens): string
    {
        return (string) preg_replace_callback(
            '/(https?:\/\/\S+|www\.\S+|[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}|\d+(?:\.\d+)+|\d+(?::\d+)+)/iu',
            function (array $matches) use (&$protectedTokens): string {
                $placeholder = '__PERSIAN_CORE_DISPLAY_TOKEN_'.count($protectedTokens).'__';
                $protectedTokens[$placeholder] = $matches[0];

                return $placeholder;
            },
            $text,
        );
    }

    private function normalizeWhitespace(string $text): string
    {
        return trim((string) preg_replace('/[ \t\r\n\f\v]+/u', ' ', $text));
    }

    private function stringValue(string|int|float|null $value): string
    {
        return $value === null ? '' : (string) $value;
    }
}
