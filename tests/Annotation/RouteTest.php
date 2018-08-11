<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Tests\Annotation;

use Cryode\Annotaction\Annotation\InvalidValueException;
use Cryode\Annotaction\Annotation\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function testCanCreateStandardRoute(): void
    {
        $route = new Route([
            'value' => '/',
            'method' => 'POST',
            'middleware' => 'web',
            'where' => [
                'id' => '\d+',
            ],
            'name' => 'my.route',
        ]);

        self::assertSame('/', $route->getPath());
        self::assertSame(['POST'], $route->getHttpMethods());
        self::assertSame(['web'], $route->getMiddleware());
        self::assertSame(['id' => '\d+'], $route->getWhere());
        self::assertSame('my.route', $route->getName());
    }

    public function testCanCreateMinimalRouteWithDefaults(): void
    {
        $route = new Route(['value' => '/path']);

        self::assertSame('/path', $route->getPath());
        self::assertSame(['GET', 'HEAD'], $route->getHttpMethods());
        self::assertSame([], $route->getMiddleware());
        self::assertSame([], $route->getWhere());
        self::assertNull($route->getName());
    }

    public function testArrayParametersCanBeInCommaDelimitedFormat()
    {
        $route = new Route([
            'path' => '',
            'method' => 'get,post',
            'middleware' => 'foo,bar',
        ]);

        self::assertSame(['GET', 'POST', 'HEAD'], $route->getHttpMethods());
        self::assertSame(['foo', 'bar'], $route->getMiddleware());
    }

    public function testPathIsRequired(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('A Path is required to use the Route Annotation');

        $route = new Route([
            'name' => 'exceptional.route',
        ]);
    }

    public function testHttpMethodIsNotEmpty(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Empty HTTP Method parameter');

        $route = new Route([
            'path' => '/',
            'method' => '',
        ]);
    }

    public function testUnknownHttpMethodThrowsError(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Unrecognized HTTP method "FOO"');

        $route = new Route([
            'path' => '/',
            'method' => 'foo',
        ]);
    }

    public function testAnyMethodCannotBeUsedWithOtherMethods(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Must use "any" HTTP method option alone, not with other methods');

        $route = new Route([
            'value' => '/',
            'method' => 'any,post',
        ]);
    }

    public function testUnknownRoutePropertyThrowsError(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Unknown property "foo" on annotation "'.Route::class.'"');

        $route = new Route([
            'path' => '/',
            'foo' => 'bar',
        ]);
    }
}
