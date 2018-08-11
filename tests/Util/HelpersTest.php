<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Tests;

use function Cryode\Annotaction\Util\comma_str_to_array;
use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testCanParseArrayOrCommaStringValueIntoArray(): void
    {
        self::assertEquals([], comma_str_to_array([]));
        self::assertEquals([], comma_str_to_array(''));
        self::assertEquals([], comma_str_to_array(null));
        self::assertEquals([], comma_str_to_array(['']));

        self::assertEquals(['get'], comma_str_to_array('get'));
        self::assertEquals(['get'], comma_str_to_array('get,'));
        self::assertEquals(['get'], comma_str_to_array(',get'));
        self::assertEquals(['get'], comma_str_to_array(['get']));

        self::assertEquals(['post', 'put'], comma_str_to_array('post,put'));
        self::assertEquals(['post', 'put'], comma_str_to_array('post, put'));
        self::assertEquals(['post', 'put'], comma_str_to_array(', post  , put '));
    }
}
