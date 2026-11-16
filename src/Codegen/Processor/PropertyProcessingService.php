<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Processor;

use Nette\PhpGenerator\Property;
use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Processor\Property\PropertyDefinitionProcessorInterface;
use Klass8s\K8SchemaGenerator\Codegen\Processor\Property\PropertyProcessorInterface;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Nette\PhpGenerator\ClassType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

final readonly class PropertyProcessingService
{
    /**
     * @param PropertyDefinitionProcessorInterface[] $definitionProcessors
     * @param PropertyProcessorInterface[] $propertyProcessors
     */
    public function __construct(
        private iterable $definitionProcessors,
        private iterable $propertyProcessors,
    ) {
    }

    public function processDefinition(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void
    {
        foreach ($this->definitionProcessors as $processor) {
            if ($processor->supports($property, $className, $class, $context)) {
                $processor->process($property, $className, $class, $context);
            }
        }
    }

    public function processProperty(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $class, Context $context): void
    {
        foreach ($this->propertyProcessors as $processor) {
            if ($processor->supports($definition, $property, $className, $class, $context)) {
                $processor->process($definition, $property, $className, $class, $context);
            }
        }
    }
}
