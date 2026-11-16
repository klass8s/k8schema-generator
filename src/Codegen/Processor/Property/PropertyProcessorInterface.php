<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Processor\Property;

use Nette\PhpGenerator\Property;
use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Nette\PhpGenerator\ClassType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

interface PropertyProcessorInterface
{
    public function process(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $classType, Context $context): void;
    public function supports(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $class, Context $context): bool;
}
