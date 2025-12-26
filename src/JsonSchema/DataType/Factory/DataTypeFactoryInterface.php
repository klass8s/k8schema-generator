<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory;

use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypesService;

interface DataTypeFactoryInterface
{
    /**
     * This method creates a data type corresponding to this concrete factory
     *
     * @param array<string, mixed> $schema
     */
    public function createFromSchema(array $schema, DataTypesService $service): DataTypeInterface;
}
