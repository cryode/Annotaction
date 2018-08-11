<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Tests\ExampleActions;

use Cryode\Annotaction\Annotation\Route;

/**
 * @Route(path="/allthethings/{thing}",
 *        method="any",
 *        middleware="web",
 *        where={"thing": "\d+"},
 *        name="allthethings")
 */
class AllTheThings
{
    public function __invoke()
    {
        //
    }
}
