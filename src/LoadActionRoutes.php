<?php

declare(strict_types=1);

namespace Cryode\Annotaction;

use Cryode\Annotaction\Annotation\InvalidActionAnnotationException;
use Cryode\Annotaction\Annotation\InvalidValueException;
use Cryode\Annotaction\Annotation\Route;
use Cryode\Annotaction\Util\FqcnFinder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;

class LoadActionRoutes
{
    /** @var Filesystem */
    private $filesystem;

    /** @var Router */
    private $router;

    /** @var string */
    private $actionDir;

    public function __construct(Filesystem $filesystem, Router $router, string $actionDir)
    {
        $this->filesystem = $filesystem;
        $this->router = $router;
        $this->actionDir = $actionDir;
    }

    public function __invoke()
    {
        if ( ! $this->filesystem->isDirectory($this->actionDir)) {
            $this->filesystem->makeDirectory($this->actionDir);
        }

        // Can be removed when upgrading to doctrine/annotations 2.0
        AnnotationRegistry::registerLoader('class_exists');

        $reader = new AnnotationReader();

        $this->router::macro('addFromAnnotation', function (string $actionFqcn, Route $annotation) {
            $this->addRoute($annotation->getHttpMethods(), $annotation->getPath(), [
                'uses' => $actionFqcn,
                'as' => $annotation->getName(),
                'middleware' => $annotation->getMiddleware(),
                'where' => $annotation->getWhere(),
            ]);
        });

        foreach ($this->filesystem->allFiles($this->actionDir, false) as $file) {
            $actionFqcn = FqcnFinder::findClass($file->getPathname());
            $reflectionClass = new \ReflectionClass($actionFqcn);

            if ( ! $reflectionClass->hasMethod('__invoke')) {
                throw new \RuntimeException('Action class '.$actionFqcn.' does not have the __invoke() method');
            }

            try {
                /** @var Route $annotation */
                $annotation = $reader->getClassAnnotation($reflectionClass, Route::class);
            } catch (InvalidValueException $e) {
                throw InvalidActionAnnotationException::make($actionFqcn, $e);
            }

            $this->router->addFromAnnotation($actionFqcn, $annotation);
        }
    }
}
