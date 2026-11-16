<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

interface PropertyDefinitionProcessorInterface
{
    public function process(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void;
    public function supports(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): bool;
}
