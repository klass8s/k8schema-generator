<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\JsonSchema\DataType\Factory;

use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypesService;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ObjectType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\PropertyDefinition;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\UnknownType;

class ObjectTypeFactory implements TypeAwareFactoryInterface
{
    private const string EXPECTED_TYPE = 'object';

    public function createFromSchema(array $schema, DataTypesService $service): ObjectType
    {
        $requiredProperties = $schema['required'] ?? [];
        $properties = [];
        foreach ($schema['properties'] ?? [] as $name => $propertySchema) {
            $required = \in_array($name, $requiredProperties);
            $type = $service->determineType($propertySchema);
            $nullable = !$type instanceof UnknownType && !$required;
            $property = new PropertyDefinition($name, $type, $required, $nullable);
            $properties[$name] = $property;

            $description = $propertySchema['description'] ?? null;
            if (null !== $description) {
                $description = \wordwrap($description, 80);
                $property->setDescription($description);
            }
        }

        $additionalPropsType = \array_key_exists('additionalProperties', $schema) && is_array($schema['additionalProperties'])
            ? $service->determineType($schema['additionalProperties'])
            : null;

        return new ObjectType($properties, $additionalPropsType);
    }

    public function expectedTypes(): array
    {
        return [ self::EXPECTED_TYPE ];
    }
}
