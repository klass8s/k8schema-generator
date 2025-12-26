<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Property;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

abstract class AbstractPropertyProcessor implements PropertyProcessorInterface
{
    public function process(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $classType, Context $context): void
    {
    }
}
