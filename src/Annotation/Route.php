<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Annotation;

use function Cryode\Annotaction\Util\comma_str_to_array;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Route
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $method = ['GET', 'HEAD'];

    /**
     * @var array
     */
    private $where = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $middleware = [];

    public function __construct(array $data)
    {
        if ( ! isset($data['path']) && ! isset($data['value'])) {
            throw new InvalidValueException('A Path is required to use the Route Annotation');
        }

        $this->setPath($data['path'] ?? $data['value']);
        unset($data['path'], $data['value']);

        if (isset($data['method'])) {
            $this->setHttpMethods($data['method']);
            unset($data['method']);
        }

        foreach ($data as $key => $value) {
            $method = 'set'.$key;
            if ( ! \method_exists($this, $method)) {
                throw new InvalidValueException(\sprintf('Unknown property "%s" on annotation "%s".', $key, \get_class($this)));
            }
            $this->$method($value);
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getHttpMethods(): array
    {
        return $this->method;
    }

    /**
     * @param string|array $httpMethods
     *
     * @return self
     */
    public function setHttpMethods($httpMethods): self
    {
        $httpMethods = \array_map('strtoupper', comma_str_to_array($httpMethods));

        $this->assertValidHttpMethods($httpMethods);

        if (\in_array('GET', $httpMethods, true) && ! \in_array('HEAD', $httpMethods, true)) {
            $httpMethods[] = 'HEAD';
        }

        $this->method = $httpMethods;

        return $this;
    }

    public function getWhere(): array
    {
        return $this->where;
    }

    public function setWhere(array $where): self
    {
        $this->where = $where;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function setMiddleware($middleware): self
    {
        $this->middleware = comma_str_to_array($middleware);

        return $this;
    }

    /**
     * @param array $httpMethods
     *
     * @throws InvalidValueException if $httpMethods is empty
     *                               if $httpMethods contains an unrecognized method
     *                               if ANY method is combined with another method
     */
    private function assertValidHttpMethods(array $httpMethods): void
    {
        if (0 === \count($httpMethods)) {
            throw new InvalidValueException('Empty HTTP Method parameter');
        }

        $validMethods = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'ANY'];

        foreach ($httpMethods as $httpMethod) {
            if ( ! \in_array($httpMethod, $validMethods, true)) {
                throw new InvalidValueException('Unrecognized HTTP method "'.$httpMethod.'"');
            }
        }

        if (\in_array('ANY', $httpMethods, true) && \count($httpMethods) > 1) {
            throw new InvalidValueException('Must use "any" HTTP method option alone, not with other methods');
        }
    }
}
