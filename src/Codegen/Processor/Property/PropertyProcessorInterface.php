<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Processor\Property;

use Nette\PhpGenerator\Property;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Nette\PhpGenerator\ClassType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

interface PropertyProcessorInterface
{
    public function process(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $classType, Context $context): void;
    public function supports(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $class, Context $context): bool;
}
