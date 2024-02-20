<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\Container\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class Container implements ContainerInterface
{
    private array $entries = [];

    public function set(string $id, callable $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function resolveDependency(
        ReflectionParameter $parameter,
        string $id
    ) {
        $name = $parameter->getName();
        $type = $parameter->getType();

        if ( ! $type) {
            throw new ContainerException(
                'Failed to resolve class "'.$id
                .'" because parameter "'.$name.'" is missing type hint'
            );
        }

        if ($type instanceof ReflectionUnionType) {
            throw new ContainerException(
                'Failed to resolve class "'.$id
                .'" because of union type for parameter "'.$name.'"'
            );
        }

        if ($type instanceof ReflectionNamedType
            && ! $type->isBuiltin()
        ) {
            return $this->get($type->getName());
        }

        throw new ContainerException(
            'Failed to resolve class "'.$id
            .'" because of parameter "'.$name.'" is not a dependency'
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws ContainerException
     */
    #[\Override] public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];

            return $entry($this);
        }

        return $this->resolve($id);
    }

    #[\Override] public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function resolve(string $id): mixed
    {
        // 1. Inspect the class that we are trying to get from the container

        if ( ! class_exists($id)) {
            throw new ContainerException('Class "'.$id.'" has not found');
        }

        $reflectionClass = new ReflectionClass($id);

        if ( ! $reflectionClass->isInstantiable()) {
            throw new ContainerException('Class "'.$id.'" is not instantiable');
        }

        // 2. Inspect constructor of the class

        $constructor = $reflectionClass->getConstructor();

        if ( ! $constructor) {
            return new $id();
        }

        // 3. Inspect constructor parameters (dependencies)

        $parameters = $constructor->getParameters();

        if (empty($parameters)) {
            return new $id();
        }

        // 4. If the constructor parameter is a class then try to resolve this class using the container

        $dependencies = array_map(
            fn($reflectionParameter) => $this->resolveDependency(
                $reflectionParameter,
                $id
            ),
            $parameters
        );
        try {
            return $reflectionClass->newInstanceArgs($dependencies);
        } catch (ReflectionException $e) {
            throw new ContainerException($e->getMessage());
        }
    }
}
