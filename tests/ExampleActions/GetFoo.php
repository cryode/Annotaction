<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Tests\ExampleActions;

use Cryode\Annotaction\Annotation\Route;

/**
 * @Route("/foo")
 */
final class GetFoo
{
    public function __invoke()
    {
        //
    }
}
