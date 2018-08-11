<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Tests\Annotation;

use Cryode\Annotaction\Annotation\InvalidActionAnnotationException;
use Cryode\Annotaction\Annotation\InvalidValueException;
use PHPUnit\Framework\TestCase;

class InvalidActionAnnotationExceptionTest extends TestCase
{
    public function testMake(): void
    {
        $valueException = new InvalidValueException('Invalid Value Exception', 123);

        $invalidActionException = InvalidActionAnnotationException::make('FooBar', $valueException);

        self::assertSame('Invalid Route Annotation in class FooBar: Invalid Value Exception', $invalidActionException->getMessage());
        self::assertSame(123, $invalidActionException->getCode());
        self::assertInstanceOf(InvalidValueException::class, $invalidActionException->getPrevious());
    }
}
