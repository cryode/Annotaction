<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Annotation;

class InvalidActionAnnotationException extends \LogicException
{
    public static function make(string $class, InvalidValueException $invalidValueException): self
    {
        $message = \sprintf(
            'Invalid Route Annotation in class %s: %s',
            $class,
            $invalidValueException->getMessage()
        );

        return new static($message, $invalidValueException->getCode(), $invalidValueException);
    }
}
