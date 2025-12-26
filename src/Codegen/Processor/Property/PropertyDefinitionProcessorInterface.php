<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

interface PropertyDefinitionProcessorInterface
{
    public function process(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void;
    public function supports(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): bool;
}
