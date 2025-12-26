<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory;

use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\BoolType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypesService;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\IntegerType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\NumberType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\StringType;

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
