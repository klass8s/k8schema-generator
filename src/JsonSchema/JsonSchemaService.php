<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema;

use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypesService;

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
