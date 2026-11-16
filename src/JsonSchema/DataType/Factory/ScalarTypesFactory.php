<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\JsonSchema\DataType\Factory;

use Klass8s\K8SchemaGenerator\JsonSchema\DataType\BoolType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypesService;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\IntegerType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\NumberType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\StringType;

class ScalarTypesFactory implements TypeAwareFactoryInterface
{
    private const array JSON_SCHEMA_TYPES_MAP = [
        'boolean' => BoolType::class,
        'integer' => IntegerType::class,
        'number'  => NumberType::class,
        'string'  => StringType::class,
    ];

    public function createFromSchema(array $schema, DataTypesService $service): DataTypeInterface
    {
        $class = self::JSON_SCHEMA_TYPES_MAP[$schema['type']];

        return new $class();
    }

    public function expectedTypes(): array
    {
        return array_keys(self::JSON_SCHEMA_TYPES_MAP);
    }
}
