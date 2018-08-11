<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Tests;

use Cryode\Annotaction\Annotation\Route;
use Cryode\Annotaction\Console\ActionMake;
use Cryode\Annotaction\Util\FqcnFinder;
use PHPUnit\Framework\TestCase;

class FqcnFinderTest extends TestCase
{
    /**
     * @dataProvider classPathProvider
     */
    public function testCanFindClassName(string $filepath, string $expectedClass): void
    {
        self::assertSame($expectedClass, FqcnFinder::findClass($filepath));
    }

    public function classPathProvider(): array
    {
        return [
            [__DIR__ . '/../../src/Annotation/Route.php', Route::class],
            [__DIR__ . '/../../src/Console/ActionMake.php', ActionMake::class],
            [__DIR__ . '/../../src/Util/FqcnFinder.php', FqcnFinder::class],
        ];
    }
}
