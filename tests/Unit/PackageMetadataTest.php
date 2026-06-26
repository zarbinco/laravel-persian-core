<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Tests\TestCase;

class PackageMetadataTest extends TestCase
{
    public function test_config_contains_release_top_level_keys(): void
    {
        $config = config('persian-core');

        $this->assertIsArray($config);

        foreach ([
            'text',
            'numbers',
            'mobile',
            'money',
            'validation',
            'developer_experience',
        ] as $key) {
            $this->assertArrayHasKey($key, $config);
        }
    }

    public function test_composer_metadata_is_release_ready(): void
    {
        $composer = $this->composerJson();

        $this->assertSame('zarbinco/laravel-persian-core', $composer['name']);
        $this->assertSame(
            'A lightweight Laravel foundation package for Persian text, numbers, mobile, money, and validation utilities.',
            $composer['description'],
        );
        $this->assertSame('https://github.com/zarbinco/laravel-persian-core', $composer['homepage']);
        $this->assertSame('MIT', $composer['license']);
        $this->assertArrayNotHasKey('version', $composer);

        $require = $composer['require'];
        $this->assertIsArray($require);
        $this->assertArrayHasKey('illuminate/validation', $require);

        $support = $composer['support'];
        $this->assertIsArray($support);
        $this->assertSame('https://github.com/zarbinco/laravel-persian-core/issues', $support['issues']);
        $this->assertSame('https://github.com/zarbinco/laravel-persian-core', $support['source']);
    }

    /** @return array<string, mixed> */
    private function composerJson(): array
    {
        $contents = file_get_contents(dirname(__DIR__, 2).'/composer.json');

        $this->assertNotFalse($contents);

        $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        $this->assertIsArray($decoded);

        return $decoded;
    }
}
