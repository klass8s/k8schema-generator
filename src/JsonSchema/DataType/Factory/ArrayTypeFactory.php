<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory;

use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ArrayType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypesService;

class ArrayTypeFactory implements TypeAwareFactoryInterface
{
    private const string EXPECTED_TYPE = 'array';

    public function createFromSchema(array $schema, DataTypesService $service): DataTypeInterface
    {
        $itemType = $service->determineType($schema['items']);

        return new ArrayType($itemType);
    }

    /**
     * @return string[]
     */
    public function expectedTypes(): array
    {
        return [ self::EXPECTED_TYPE ];
    }
}
