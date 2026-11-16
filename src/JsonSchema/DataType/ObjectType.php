<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\JsonSchema\DataType;

final readonly class ObjectType extends AbstractType
{
    /**
     * @param DataTypeInterface[]|array<string, DataTypeInterface> $properties
     * @param ?DataTypeInterface $additionalPropertiesType
     */
    public function __construct(
        protected array $properties,
        private ?DataTypeInterface $additionalPropertiesType
    ) {
    }

    /**
     * @return PropertyDefinition[]
     */
    public function properties(): iterable
    {
        return $this->properties;
    }

    /**
     * @return ?DataTypeInterface
     */
    public function additionalPropertiesType(): ?DataTypeInterface
    {
        return $this->additionalPropertiesType;
    }

    public function allowsAdditionalProperties(): bool
    {
        return null !== $this->additionalPropertiesType;
    }

    public function hasProperty(string $name): bool
    {
        return \array_key_exists($name, $this->properties);
    }

    public function getPropertyType(string $name): DataTypeInterface
    {
        return $this->properties[$name];
    }
}
