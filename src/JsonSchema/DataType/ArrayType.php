<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType;

final readonly class ArrayType extends AbstractType
{
    public const string NAME = 'array';

    public function __construct(private DataTypeInterface $itemType)
    {
    }

    public function itemType(): DataTypeInterface
    {
        return $this->itemType;
    }
}
