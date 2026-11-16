<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\JsonSchema\DataType\Factory;

use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ArrayType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypesService;

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
