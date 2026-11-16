<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen;

use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;

final readonly class GeneratedClassesCache implements \IteratorAggregate
{
    private \ArrayObject $map;

    public function __construct()
    {
        $this->map = new \ArrayObject([]);
    }

    public function has(string $fcqn): bool
    {
        return $this->map->offsetExists($fcqn);
    }

    public function get(string $fcqn): PHPClass
    {
        return $this->map[$fcqn];
    }

    public function set(string $fqcn, PHPClass $class): void
    {
        $this->map->offsetSet($fqcn, $class);
    }

    public function getIterator(): \Traversable
    {
        return $this->map;
    }
}
