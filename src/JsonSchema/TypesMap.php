<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema;

use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;

class TypesMap implements \IteratorAggregate
{
    /**
     * @var \ArrayObject<string, DataTypeInterface>
     */
    private \ArrayObject $map;

    /**
     * @param array<string,DataTypeInterface> $typesMap
     */
    public function __construct(array $typesMap)
    {
        $this->map = new \ArrayObject($typesMap);
    }

    public function get(string $name): DataTypeInterface
    {
        return $this->map[$name];
    }

    public function has(string $name): bool
    {
        return $this->map->offsetExists($name);
    }

    public function filter(callable $callback): TypesMap
    {
        $newMap = array_filter(
            $this->getIterator()->getArrayCopy(),
            static fn (DataTypeInterface $type, string $name): bool => $callback($type, $name),
            ARRAY_FILTER_USE_BOTH,
        );

        return new self($newMap);
    }

    public function set(string $name, DataTypeInterface $type): void
    {
        $this->map[$name] = $type;
    }

    /**
     * @return \ArrayObject<string, DataTypeInterface>
     */
    public function getIterator(): \Traversable
    {
        return $this->map;
    }
}
