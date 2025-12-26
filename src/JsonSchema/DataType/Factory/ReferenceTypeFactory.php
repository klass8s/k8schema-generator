<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory;

use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypesService;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ReferenceType;

class ReferenceTypeFactory implements TypeUnawareFactoryInterface
{
    public function createFromSchema(array $schema, DataTypesService $service): DataTypeInterface
    {
        $ref = $schema['$ref'];
        $lastSlashPos = strripos($ref, '/');
        $definitionName = substr($ref, $lastSlashPos + 1);

        return new ReferenceType($definitionName);
    }

    public function recognizesSchema(array $schema): bool
    {
        return array_key_exists('$ref', $schema);
    }
}
