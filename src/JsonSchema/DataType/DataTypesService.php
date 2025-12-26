<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType;

use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory\DataTypeFactoryInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory\TypeAwareFactoryInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory\TypeUnawareFactoryInterface;

final class DataTypesService
{
    /**
     * @var array<string, DataTypeFactoryInterface>
     */
    private array $typedFactoriesMap;

    /**
     * @param DataTypeFactoryInterface[] $factories
     */
    public function __construct(private readonly iterable $factories)
    {
    }

    public function determineType(array $jsonSchema): DataTypeInterface
    {
        $typedFactoriesMap = $this->typedFactoriesMap();
        if (\array_key_exists('type', $jsonSchema)) {
            $typeName = $jsonSchema['type'];
            $this->ensureValidTypeName($typeName);
            $factory = $typedFactoriesMap[$typeName];

            return $factory->createFromSchema($jsonSchema, $this)->setSchema($jsonSchema);
        }

        foreach ($this->factories as $factory) {
            if (!$factory instanceof TypeUnawareFactoryInterface) {
                continue; // This schema is not typed
            }

            if ($factory->recognizesSchema($jsonSchema)) {
                return $factory->createFromSchema($jsonSchema, $this)->setSchema($jsonSchema);
            }
        }

        return UnknownType::fromJsonSchema($jsonSchema);
    }

    private function ensureValidTypeName(string $typeName): void
    {
        if (!array_key_exists($typeName, $this->typedFactoriesMap())) {
            throw new \InvalidArgumentException(sprintf('Unknown type name "%s"', $typeName));
        }
    }

    /**
     * @return array<string, DataTypeFactoryInterface>
     */
    private function typedFactoriesMap(): array
    {
        return $this->typedFactoriesMap ??= $this->buildTypedFactoriesMap();
    }

    /**
     * @return array<string, DataTypeFactoryInterface>
     */
    #[\NoDiscard]
    private function buildTypedFactoriesMap(): array
    {
        $typedFactoriesMap = [];
        foreach ($this->factories as $factory) {
            if (!$factory instanceof TypeAwareFactoryInterface) {
                continue;
            }

            $expectedTypes = $factory->expectedTypes();
            foreach ($expectedTypes as $expectedType) {
                if (array_key_exists($expectedType, $typedFactoriesMap)) {
                    throw new \LogicException(sprintf('Factories "%s" and "%s" both expect type "%s"', $factory::class, get_class($typedFactoriesMap[$expectedType]), $expectedType));
                }
                $typedFactoriesMap[$expectedType] = $factory;
            }
        }

        return $typedFactoriesMap;
    }
}
