<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory;

use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\AbstractUnionType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\AllOfType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\AnyOfType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypesService;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\OneOfType;

class UnionTypesFactory implements TypeUnawareFactoryInterface
{
    private const array SUPPORTED_TYPES_MAP = [
        'allOf' => AllOfType::class,
        'anyOf' => AnyOfType::class,
        'oneOf' => OneOfType::class,
    ];

    public function createFromSchema(array $schema, DataTypesService $service): AbstractUnionType
    {
        $propertyName = $this->matchedPropertyName($schema);
        $types = [];
        foreach ($schema[$propertyName] as $itemSchema) {
            $types[] = $service->determineType($itemSchema);
        }
        $typeClass = self::SUPPORTED_TYPES_MAP[$propertyName];

        return new $typeClass($types);
    }

    public function recognizesSchema(array $schema): bool
    {
        return null !== $this->matchedPropertyName($schema);
    }

    private function matchedPropertyName(array $schema): ?string
    {
        $intersection = \array_intersect_key(self::SUPPORTED_TYPES_MAP, $schema);

        return key($intersection);
    }
}
