<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Tests;

use Cryode\Annotaction\LoadActionRoutes;
use Cryode\Annotaction\Tests\ExampleActions\AllTheThings;
use Cryode\Annotaction\Tests\ExampleActions\GetFoo;
use Cryode\Annotaction\Tests\ExampleActions\PostFoo;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase;

class LoadActionRoutesTest extends TestCase
{
    public function testActionLoad(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        $loader = new LoadActionRoutes(
            $this->app->make(Filesystem::class),
            $router,
            __DIR__ . '/ExampleActions'
        );

        $loader();

        $routes = $router->getRoutes();

        self::assertSame(3, $routes->count());

        $getFoo = $routes->getByAction(GetFoo::class);

        self::assertSame('foo', $getFoo->uri());
        self::assertSame(['GET', 'HEAD'], $getFoo->methods());
        self::assertNull($getFoo->getName());

        $postFoo = $routes->getByAction(PostFoo::class);

        self::assertSame('foo', $postFoo->uri());
        self::assertSame(['POST'], $postFoo->methods());
        self::assertNull($postFoo->getName());

        $allthethings = $routes->getByAction(AllTheThings::class);

        self::assertSame('allthethings/{thing}', $allthethings->uri());
        self::assertSame(['ANY'], $allthethings->methods());
        self::assertSame(['web'], $allthethings->middleware());
        self::assertSame(['thing' => '\d+'], $allthethings->wheres);
        self::assertSame('allthethings', $allthethings->getName());
    }
}
