<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Processor\Property;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ArrayType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ObjectType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

class ObjectPropertyProcessor extends AbstractPropertyDefinitionProcessor
{
    public function process(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void
    {
        $property->setNullable(false);
        $initializer = function (Property $property, Method $constructor): void {
            $property
                ->setNullable(false)
                ->setInitialized(false);

            $propertyType = $property->getType();
            $propertyValue = 'array' === $propertyType
                ? '[]'
                : sprintf('new %s()', $propertyType);

            $constructor->addBody(
                sprintf('$this->%s = %s;', $property->getName(), $propertyValue)
            );
        };

        $property->setInitializer($initializer);
    }

    public function supports(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): bool
    {
        $type = $property->type();

        return ($type instanceof ObjectType && 0 === count($type->properties()) && 'metadata' !== $property->name()) || $type instanceof ArrayType;
    }
}
