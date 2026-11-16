<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\JsonSchema;

use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypesService;

final readonly class JsonSchemaService
{
    public function __construct(private DataTypesService $typesService)
    {
    }

    public function typesMap(array $jsonSchema): TypesMap
    {
        $map = array_map(
            fn (array $definition) => $this->typesService->determineType($definition),
            $jsonSchema['definitions'],
        );

        return new TypesMap($map);
    }
}
