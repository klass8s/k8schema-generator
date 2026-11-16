<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\JsonSchema\DataType;

abstract readonly class AbstractUnionType extends AbstractType
{
    /**
     * @param DataTypeInterface[] $types
     */
    public function __construct(public array $types)
    {
    }

    /**
     * @return DataTypeInterface[]
     */
    public function types(): array
    {
        return $this->types;
    }
}
