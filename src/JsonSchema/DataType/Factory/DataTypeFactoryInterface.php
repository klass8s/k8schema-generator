<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\JsonSchema\DataType\Factory;

use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypesService;

interface DataTypeFactoryInterface
{
    /**
     * This method creates a data type corresponding to this concrete factory
     *
     * @param array<string, mixed> $schema
     */
    public function createFromSchema(array $schema, DataTypesService $service): DataTypeInterface;
}
