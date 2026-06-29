<?php

namespace Zarbinco\PersianCore\Tests\Unit;

use Zarbinco\PersianCore\Facades\Persian;
use Zarbinco\PersianCore\Tests\TestCase;

class PersianSearchFacadeTest extends TestCase
{
    public function test_search_fluent_api_normalizes_value(): void
    {
        $this->assertSame(
            'اب میوه سن ایچ 12500 تومان',
            Persian::search("آب\u{200C}میوه سن\u{200C}ایچ ۱۲,۵۰۰ تومان!")->normalize(),
        );
    }

    public function test_search_fluent_api_returns_tokens(): void
    {
        $this->assertSame(
            ['اب', 'میوه', 'سن', 'ایچ'],
            Persian::search("آب\u{200C}میوه سن\u{200C}ایچ")->tokens(),
        );
    }

    public function test_search_fluent_api_value_aliases_normalize(): void
    {
        $this->assertSame('کتاب های خوب', Persian::search("کتاب\u{200C}های خوب")->value());
    }

    public function test_searchable_delegates_to_search_normalizer(): void
    {
        $this->assertSame('اب میوه 12500', Persian::searchable("آب\u{200C}میوه ۱۲,۵۰۰"));
    }
}
